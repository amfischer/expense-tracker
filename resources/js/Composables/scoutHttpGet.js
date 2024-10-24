import { useDebounceFn } from '@vueuse/core';
import { useForm, usePage } from '@inertiajs/vue3';
import { onMounted, toValue } from 'vue';
import { parse } from 'qs';

export function useScoutHttpGet({ url }) {
    const form = useForm({
        query: '',
        sort_by: '',
        sort_dir: '',
        filters: {},
    });

    const clearQuery = () => {
        form.query = '';
        search();
    };

    // const toast = useToastStore();

    /**
     * FULL PAGE LOAD
     */
    onMounted(() => {
        // grab url params & set form data
        const params = parse(document.location.search, { ignoreQueryPrefix: true });

        form.query = params.query || '';
        form.sort_by = params.sort_by || '';
        form.sort_dir = params.sort_dir || '';
        form.filters = params.filters || {};

        // toggle any validation errors from bad query params
        if (usePage().props.errors.scout !== undefined) {
            // toast.setErrorMessage(usePage().props.errors.scout[0]);
            console.error('search errors: ', usePage().props.errors.scout);
        }
    });

    /**
     * FILTERS
     */

    /**
     * Toggle a boolean filter
     * On  - add filter name as property in form.filters and set to 1
     * Off - remove property from form.filters
     *
     * @param {string} payload The filter name
     */
    const toggleBooleanFilter = (payload) => {
        if (Object.hasOwn(form.filters, payload)) {
            delete form.filters[payload];
        } else {
            form.filters[payload] = 1;
        }

        search();
        return;
    };

    /**
     * Toggle options in a filter that's an array
     *
     * @param {string} key The filter name
     * @param {string} option The option being toggled
     */
    const toggleArrayFilter = (key, option) => {
        // If filter doesn't exist, create with <key> as property name, and add option to a new array
        if (!Object.hasOwn(form.filters, key)) {
            form.filters[key] = [option];
            search();
            return;
        }

        // Filter exists, toggle option

        // 1. If option is missing, push to the array & search
        if (!form.filters[key].includes(option)) {
            form.filters[key].push(option);
            search();
            return;
        }

        // 2. If option exists, remove from array and delete filter if array is empty
        let index = form.filters[key].indexOf(option);
        form.filters[key].splice(index, 1);

        if (form.filters[key].length === 0) {
            delete form.filters[key];
        }

        search();
    };

    const clearFilters = () => {
        form.filters = {};
        search();
    };

    /**
     * SORTING
     */
    const sortBy = (payload) => {
        // new column was clicked
        if (form.sort_by !== payload) {
            form.sort_by = payload;
            form.sort_dir = 'asc';

            search();
            return;
        }

        // same column was clicked, toggle direction
        if (form.sort_dir === 'asc') {
            form.sort_dir = 'desc';

            search();
            return;
        }

        // same column was clicked, clear sort
        form.sort_by = '';
        form.sort_dir = '';

        search();
        return;
    };

    /**
     * SEARCH
     */
    const search = useDebounceFn(() => {
        form.transform((data) => {
            if (data.sort_by === '') {
                delete data.sort_by;
            }
            if (data.sort_dir === '') {
                delete data.sort_dir;
            }
            return data;
        }).get(toValue(url), {
            preserveState: true,
            preserveScroll: true,
            errorBag: 'scout',
            onSuccess: (resp) => {
                console.log('success', resp);
            },
            onError: (err) => {
                console.log('test', err, Object.values(err));
                // toast.setErrorMessage(Object.values(err)[0]);
            },
        });
    }, 400);

    return {
        form,
        toggleBooleanFilter,
        toggleArrayFilter,
        sortBy,
        search,
        clearQuery,
        clearFilters,
    };
}

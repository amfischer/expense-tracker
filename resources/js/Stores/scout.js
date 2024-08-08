import { useForm } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useScoutStore = defineStore('scout', () => {
    const form = useForm({
        query: '',
        date: [],
        sort_by: 'effective_date',
        category_ids: [],
        payment_methods: [],
    });

    // should avoid onMounted() in pinia
    // https://github.com/vuejs/pinia/discussions/1508

    const clearSearchQuery = () => {
        form.query = '';
        search();
    };

    const clearFilters = () => {
        form.date = [];
        form.category_ids = [];
        form.payment_methods = [];
        search();
    };

    const throttledSearch = useDebounceFn(() => {
        form.get(route('expenses.index'), {
            preserveState: true,
            onSuccess: (resp) => {
                // console.log('success', resp);
            },
            onError: (err) => {
                console.error('whoops', err);
            },
        });
    }, 400);

    const search = () => {
        form.get(route('expenses.index'), {
            preserveState: true,
            onSuccess: (resp) => {
                // console.log('sucess', resp);
            },
            onError: (err) => {
                console.log('whoops', err);
            },
        });
    };

    const options = ref({
        categories: [],
        paymentMethods: [],
    });

    const setCategories = (payload) => (options.value.categories = payload);
    const setPaymentMethods = (payload) => (options.value.paymentMethods = payload);

    return {
        form,
        options,
        throttledSearch,
        search,
        clearSearchQuery,
        clearFilters,
        setCategories,
        setPaymentMethods,
    };
});

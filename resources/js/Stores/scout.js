import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useScoutStore = defineStore('scout', () => {
    const query = ref('');
    const categoryIds = ref([]);
    const sortBy = ref('effective_date');
    const paymentMethods = ref([]);

    const runSearch = useDebounceFn(() => {
        const payload = {
            query: query.value,
            category_ids: categoryIds.value,
            sort_by: sortBy.value,
            payment_methods: paymentMethods.value,
        };
        router.get(route('expenses.index'), payload, {
            preserveState: true,
            onSuccess: (resp) => {
                // console.log('success', resp);
            },
        });
    }, 400);

    const applyFilters = () => {
        const payload = {
            query: query.value,
            category_ids: categoryIds.value,
            sort_by: sortBy.value,
            payment_methods: paymentMethods.value,
        };
        router.get(route('expenses.index'), payload, {
            preserveState: true,
        });
    };

    const options = ref({
        categories: [],
        paymentMethods: [],
    });

    const setCategories = (payload) => (options.value.categories = payload);
    const setPaymentMethods = (payload) => (options.value.paymentMethods = payload);

    return {
        query,
        categoryIds,
        paymentMethods,
        sortBy,
        options,
        runSearch,
        applyFilters,
        setCategories,
        setPaymentMethods,
    };
});

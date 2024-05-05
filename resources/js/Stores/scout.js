import { useForm } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useScoutStore = defineStore('scout', () => {
    const form = useForm({
        query: '',
        sort_by: 'effective_date',
        category_ids: [],
        payment_methods: [],
    });

    const clearFilters = () => {
        form.category_ids = [];
        form.payment_methods = [];
        search();
    }

    const throttledSearch = useDebounceFn(() => {
        form.get(route('expenses.index'), {
            preserveState: true,
            onSuccess: (resp) => {
                // console.log('success', resp);
            },
        });
    }, 400);

    const search = () => {
        form.get(route('expenses.index'), {
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
        form,
        options,
        throttledSearch,
        search,
        clearFilters,
        setCategories,
        setPaymentMethods,
    };
});

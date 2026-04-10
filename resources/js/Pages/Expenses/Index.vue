<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ExpensesTable from './Partials/Table/Table.vue';
import { Head } from '@inertiajs/vue3';
import { provide } from 'vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    expenses: Object,
    categories: Array,
    paymentMethods: Array,
    currencies: Array,
});

provide('categories', props.categories);
provide('paymentMethods', props.paymentMethods);
provide('currencies', props.currencies);
</script>

<template>
    <Head title="Expenses" />

    <div class="mx-auto w-full py-12 sm:max-w-2xl sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="mx-2 rounded-lg bg-white px-2 py-10 shadow-sm sm:mx-0 sm:p-10">
            <ExpensesTable :expenses="expenses" />
        </div>
    </div>
</template>

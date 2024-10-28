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

    <div class="w-full sm:max-w-2xl lg:max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <div class="rounded-lg shadow bg-white px-2 mx-2 py-10 sm:mx-0 sm:p-10">
            <ExpensesTable :expenses="expenses" />
        </div>
    </div>
</template>

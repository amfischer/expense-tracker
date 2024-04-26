<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AddExpenseForm from './Partials/AddExpenseForm.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

defineProps({
    categories: Array,
    tags: Array,
    currencies: Array,
    paymentMethods: Object,
});

const breadcrumbs = [{ name: 'Expenses', href: route('expenses.index'), current: false }];
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add Expense</h2>
            <Breadcrumbs class="pt-8 pb-0" :pages="breadcrumbs" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="rounded-lg shadow bg-white px-2 mx-2 py-10 sm:mx-0 sm:p-10">
                    <AddExpenseForm :categories="categories" :tags="tags" :currencies="currencies" :payment-methods="paymentMethods" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import EditExpenseForm from './Partials/EditExpenseForm.vue';
import DeleteExpenseForm from './Partials/DeleteExpenseForm.vue';
import ReceiptManager from './Partials/ReceiptManager.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    expense: Object,
    categories: Array,
    currencies: Array,
    paymentMethods: Array,
    receipt: Object,
});

const breadcrumbs = [{ name: 'Expenses', href: route('expenses.index'), current: false }];
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between px-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Expense</h2>
                <Breadcrumbs :pages="breadcrumbs" />
            </div>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="rounded-lg shadow bg-white px-2 mx-2 py-10 sm:mx-0 sm:p-10">
                    <EditExpenseForm
                        :expense="expense"
                        :categories="categories"
                        :currencies="currencies"
                        :payment-methods="paymentMethods" />
                </div>
                <div class="rounded-lg shadow bg-white px-2 mx-2 py-10 sm:mx-0 sm:p-10">
                    <ReceiptManager :expense="expense" :receipt="receipt" />
                </div>
                <div class="rounded-lg shadow bg-white px-2 mx-2 py-10 sm:mx-0 sm:p-10">
                    <DeleteExpenseForm :expense="expense" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

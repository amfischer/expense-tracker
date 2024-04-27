<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EditExpenseForm from './Partials/EditExpenseForm.vue';
import DeleteExpenseForm from './Partials/DeleteExpenseForm.vue';
import AddReceiptForm from './Partials/AddReceiptForm.vue';
import ShowReceipt from './Partials/ShowReceipt.vue';
import DeleteReceiptModal from './Partials/DeleteReceiptModal.vue';
import { useReceiptStore } from '@/Stores/receipt';
import { Head } from '@inertiajs/vue3';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
    expense: Object,
    categories: Array,
    currencies: Array,
    paymentMethods: Object,
    receipt: Object,
});

const receiptStore = useReceiptStore();
receiptStore.setExpense(props.expense);
receiptStore.setReceipt(props.receipt);

const breadcrumbs = [{ name: 'Expenses', href: route('expenses.index'), current: false }];
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Expense</h2>
            <Breadcrumbs class="pt-8 pb-0" :pages="breadcrumbs" />
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
                    <header class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Manage Receipt(s)</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Currently, only one receipt per expense is allowed.
                        </p>
                    </header>
                    <ShowReceipt v-if="receiptStore.receipt !== null" />
                    <AddReceiptForm v-else />
                </div>

                <div class="rounded-lg shadow bg-white px-2 mx-2 py-10 sm:mx-0 sm:p-10">
                    <DeleteExpenseForm :expense="expense" />
                </div>
            </div>
        </div>

        <DeleteReceiptModal />
    </AuthenticatedLayout>
</template>

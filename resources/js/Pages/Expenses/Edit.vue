<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AlertSuccess from '@/Components/Alerts/Success.vue';
import EditExpenseForm from './Partials/EditExpenseForm.vue';
import DeleteExpenseForm from './Partials/DeleteExpenseForm.vue';
import AddReceiptForm from './Partials/AddReceiptForm.vue';
import ShowReceipt from './Partials/ShowReceipt.vue';
import DeleteReceiptModal from './Partials/DeleteReceiptModal.vue';
import { useReceiptStore } from '@/Stores/receipt';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
    expense: Object,
    categories: Array,
    currencies: Array,
    receipt: Object,
});

const hasReceipt = computed(() => {
    return props.receipt !== null;
});

const receiptStore = useReceiptStore();

onMounted(() => {
    receiptStore.setExpense(props.expense);
});

const breadcrumbs = [{ name: 'Expenses', href: route('expenses.index'), current: false }];
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Expense</h2>
            <Breadcrumbs class="pt-8 pb-0" :pages="breadcrumbs" />
        </template>

        <div class="relative">
            <AlertSuccess class="max-w-5xl w-1/3 absolute top-7 left-0 right-0 mx-auto" />

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <EditExpenseForm :expense="expense" :categories="categories" :currencies="currencies" />
                    </div>

                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <header class="mb-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Manage Receipt(s)</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            </p>
                        </header>
                        <ShowReceipt v-if="hasReceipt" :receipt="receipt" />
                        <AddReceiptForm v-else />
                    </div>

                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <DeleteExpenseForm :expense="expense" />
                    </div>
                </div>
            </div>
        </div>

        <DeleteReceiptModal />
    </AuthenticatedLayout>
</template>

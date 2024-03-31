<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    expense: Object,
});

const showConfirmModal = ref(false);

const deleteExpense = () => {
    router.delete(route('expenses.delete', props.expense.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: (err) => {
            console.log('expense delete error', err);
        },
    });
};

const closeModal = () => {
    showConfirmModal.value = false;
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete Expense</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            </p>
        </header>

        <DangerButton @click="showConfirmModal = true">Delete Expense</DangerButton>

        <Modal :show="showConfirmModal" max-width="md" @close="closeModal">
            <template #header> Are you sure you want to delete this expense? </template>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ expense.payee }}
            </p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ expense.amount_pretty }}
            </p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ expense.transaction_date }}
            </p>

            <div class="mt-6 flex justify-end">
                <DangerButton class="ms-3" @click="deleteExpense"> Delete Expense </DangerButton>
            </div>
        </Modal>
    </section>
</template>

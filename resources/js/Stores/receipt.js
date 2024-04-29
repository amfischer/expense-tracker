import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useReceiptStore = defineStore('receipt', () => {
    const expense = ref(null);
    const receipt = ref(null);

    const setExpense = (payload) => {
        expense.value = payload;
    }

    const setReceipt = (payload) => {
        receipt.value = payload;
    }

    const showDeleteModal = ref(false);

    const openDeleteModal = () => {
        showDeleteModal.value = true;
    };

    const closeDeleteModal = () => {
        showDeleteModal.value = false;
    };

    return {
        expense,
        receipt,
        setExpense,
        setReceipt,
        showDeleteModal,
        openDeleteModal,
        closeDeleteModal,
    };
});

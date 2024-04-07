import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useReceiptStore = defineStore('ReceiptStore', () => {
    const expense = ref(null);
    const receipt = ref(null);

    const setExpense = (payload) => {
        expense.value = payload;
    }

    const showDeleteModal = ref(false);

    const openDeleteModal = (payload) => {
        showDeleteModal.value = true;
        receipt.value = payload;
    };

    const closeDeleteModal = () => {
        showDeleteModal.value = false;
        receipt.value = null
    };

    return {
        expense,
        receipt,
        setExpense,
        showDeleteModal,
        openDeleteModal,
        closeDeleteModal,
    };
});

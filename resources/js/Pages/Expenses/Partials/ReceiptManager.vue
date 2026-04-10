<script setup>
import { DocumentTextIcon, MinusCircleIcon } from '@heroicons/vue/24/solid';
import AddReceiptForm from './AddReceiptForm.vue';
import ShowReceiptModal from './ShowReceiptModal.vue';
import DeleteReceiptModal from './DeleteReceiptModal.vue';
import { ref } from 'vue';

defineProps({
    expense: Object,
});

const selectedReceipt = ref(null);
const showViewModal = ref(false);
const showDeleteModal = ref(false);

const openViewModal = (receipt) => {
    selectedReceipt.value = receipt;
    showViewModal.value = true;
};

const openDeleteModal = (receipt) => {
    selectedReceipt.value = receipt;
    showDeleteModal.value = true;
};
</script>

<template>
    <header class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Manage Receipt(s)</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Upload and manage receipts for this expense.</p>
    </header>

    <div class="mb-6 space-y-3" v-if="expense.receipts.length > 0">
        <div
            v-for="receipt in expense.receipts"
            :key="receipt.id"
            class="flex items-center gap-4 rounded-lg border border-gray-300 px-3 py-2 md:w-1/2">
            <DocumentTextIcon class="h-7 w-7" />
            <div
                class="flex grow cursor-pointer flex-col underline-offset-2 hover:underline"
                @click="openViewModal(receipt)">
                <span class="text-md text-gray-900">{{ receipt.mimetype }}</span>
                <span class="text-sm text-gray-600">{{ receipt.size_formatted }}</span>
            </div>
            <MinusCircleIcon class="h-5 w-5 cursor-pointer text-red-500" @click="openDeleteModal(receipt)" />
        </div>
    </div>

    <AddReceiptForm :expense="expense" />

    <ShowReceiptModal v-if="selectedReceipt" v-model="showViewModal" :expense="expense" :receipt="selectedReceipt" />
    <DeleteReceiptModal
        v-if="selectedReceipt"
        v-model="showDeleteModal"
        :expense="expense"
        :receipt="selectedReceipt" />
</template>

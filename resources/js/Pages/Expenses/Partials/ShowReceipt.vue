<script setup>
import { DocumentTextIcon, MinusCircleIcon } from '@heroicons/vue/24/solid';
import Modal from '@/Components/Modal.vue';
import { useReceiptStore } from '@/Stores/receipt';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';

const store = useReceiptStore();
const { receipt, expense } = storeToRefs(store);

const showModal = ref(false);

const src = route('expenses.receipts.show', { expense: expense.value.id, receipt: receipt.value.id });
</script>

<template>
    <div class="flex items-center gap-4 rounded-lg border border-gray-300 px-3 py-2 md:w-1/2">
        <DocumentTextIcon class="h-7 w-7" />
        <div class="grow flex flex-col cursor-pointer hover:underline underline-offset-2" @click="showModal = true">
            <span class="text-md text-gray-900">{{ receipt.mimetype }}</span>
            <span class="text-sm text-gray-600">{{ receipt.size_formatted }}</span>
        </div>
        <MinusCircleIcon class="h-5 w-5 text-red-500 cursor-pointer" @click="store.openDeleteModal()" />
    </div>

    <Modal
        :show="showModal"
        @close="showModal = false"
        :max-width="receipt.is_image ? '2xl' : '6xl'"
        :dialog-panel-classes="receipt.is_image ? 'overflow-y-scroll p-0' : 'h-screen p-0'">
        <img v-if="receipt.is_image" :src="src" />
        <iframe v-else :src="src" width="100%" height="100%" />
    </Modal>
</template>

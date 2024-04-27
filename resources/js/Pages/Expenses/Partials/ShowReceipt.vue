<script setup>
import { DocumentTextIcon, MinusCircleIcon } from '@heroicons/vue/24/solid';
import Modal from '@/Components/Modal.vue';
import { useReceiptStore } from '@/Stores/receipt';
import { computed, ref } from 'vue';
import { storeToRefs } from 'pinia';
import InputError from '@/Components/InputError.vue';

const store = useReceiptStore();
const { receipt, expense } = storeToRefs(store);

const showModal = ref(false);
const base64Raw = ref('');
const base64String = computed(() => {
    if (receipt.is_image) {
        return 'data:image;base64,' + base64Raw.value;
    }

    return 'data:application/pdf;base64,' + base64Raw.value;
});

const errorMessage = ref('');

const toggleModal = async () => {
    if (base64Raw.value !== '') {
        showModal.value = true;
        return;
    }

    try {
        const response = await fetch(
            route('expenses.receipts.base64', { expense: expense.value.id, receipt: receipt.value.id }),
        );

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        const data = await response.json();
        base64Raw.value = data.base64;
        showModal.value = true;
        errorMessage.value = '';
    } catch (error) {
        errorMessage.value = error.message;
    }
};
</script>

<template>
    <div class="flex items-center gap-4 rounded-lg border border-gray-300 px-3 py-2 md:w-1/2">
        <DocumentTextIcon class="h-7 w-7" />
        <div class="grow flex flex-col cursor-pointer hover:underline underline-offset-2" @click="toggleModal">
            <span class="text-md text-gray-900">{{ receipt.mimetype }}</span>
            <span class="text-sm text-gray-600">{{ receipt.size_formatted }}</span>
        </div>
        <MinusCircleIcon class="h-5 w-5 text-red-500 cursor-pointer" @click="store.openDeleteModal()" />
    </div>

    <InputError class="mt-2" :message="errorMessage" />

    <Modal
        :show="showModal"
        @close="showModal = false"
        :max-width="receipt.is_image ? '2xl' : '6xl'"
        :dialog-panel-classes="receipt.is_image ? '' : 'h-screen my-10'">
        <img v-if="receipt.is_image" :src="base64String" />
        <iframe v-else :src="base64String" width="100%" height="100%" />
    </Modal>
</template>

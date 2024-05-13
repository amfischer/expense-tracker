<script setup>
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { useAlertStore } from '@/Stores/alert';
import { useReceiptStore } from '@/Stores/receipt';
import { storeToRefs } from 'pinia';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const receiptStore = useReceiptStore();
const { expense, receipt, showDeleteModal } = storeToRefs(receiptStore);
const { closeDeleteModal } = receiptStore;

const alert = useAlertStore();

const processing = ref(false);
const errorMessage = ref('');

const deleteReceipt = () => {
    processing.value = true;

    router.delete(route('expenses.receipts.delete', { expense: expense.value.id, receipt: receipt.value.id }), {
        preserveScroll: true,
        onSuccess: (resp) => {
            alert.setMessage(resp.props.flash.message);
            receiptStore.setReceipt(null);
            errorMessage.value = '';
            closeDeleteModal();
        },
        onError: (err) => {
            console.log('errors', err);
            errorMessage.value = Object.values(err)[0];
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <Modal :show="showDeleteModal" max-width="md" @close="closeDeleteModal">
        <template #header>Are you sure you want to delete this receipt?</template>

        <form @submit.prevent="deleteReceipt">
            <InputError class="mt-2" :message="errorMessage" />
            <div class="flex items-center justify-end gap-4 w-full mt-10">
                <SecondaryButton type="button" :disabled="processing" @click="closeDeleteModal">No</SecondaryButton>
                <DangerButton type="submit" :disabled="processing">Delete</DangerButton>
            </div>
        </form>
    </Modal>
</template>

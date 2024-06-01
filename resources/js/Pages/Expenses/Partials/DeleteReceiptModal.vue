<script setup>
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { useAlertStore } from '@/Stores/alert';
import { useReceiptStore } from '@/Stores/receipt';
import { storeToRefs } from 'pinia';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const receiptStore = useReceiptStore();
const { expense, receipt, showDeleteModal } = storeToRefs(receiptStore);
const { closeDeleteModal } = receiptStore;

const form = useForm({
    password: '',
});

const closeModal = () => {
    closeDeleteModal();
    form.reset();
    form.clearErrors();
}

const alert = useAlertStore();

const deleteReceipt = () => {
    form.delete(route('expenses.receipts.delete', { expense: expense.value.id, receipt: receipt.value.id }), {
        preserveScroll: true,
        onSuccess: (resp) => {
            alert.setMessage(resp.props.flash.message);
            receiptStore.setReceipt(null);
            closeModal();
        },
        onError: (err) => {
            console.log('errors', err);
        },
    });
};
</script>

<template>
    <Modal :show="showDeleteModal" max-width="md" @close="closeModal">
        <template #header>Are you sure you want to delete this receipt?</template>

        <template #description>
            <p class="text-sm font-bold text-gray-600">{{ receipt?.mimetype }}, {{ receipt?.size_formatted }}</p>
            <p class="mt-3 text-sm text-gray-600">
                Please enter your password to confirm you would like to permanently delete this receipt.
            </p>
            <div class="mt-6">
                <InputLabel for="password" value="Password" class="sr-only" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="Password"
                    @keyup.enter="deleteReceipt" />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>
        </template>

        <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
            <DangerButton class="ms-3" @click="deleteReceipt"> Delete Receipt </DangerButton>
        </div>
    </Modal>
</template>

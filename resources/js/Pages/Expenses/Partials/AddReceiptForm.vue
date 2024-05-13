<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import FileInput from '@/Components/Forms/FileInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { useAlertStore } from '@/Stores/alert';
import { useReceiptStore } from '@/Stores/receipt';
import { watch } from 'vue';

const form = useForm({
    receipt_upload: '',
});

watch(
    () => form.receipt_upload,
    () => form.clearErrors(),
);

const receiptStore = useReceiptStore();
const alert = useAlertStore();

const upload = () => {
    form.post(route('expenses.receipts.store', receiptStore.expense.id), {
        preserveScroll: true,
        onSuccess: (resp) => {
            form.reset();
            alert.setMessage(resp.props.flash.message);
            receiptStore.setReceipt(resp.props.receipt);
        },
        onError: () => {
            console.log('errors', form.errors);
        },
    });
};
</script>

<template>
    <form @submit.prevent="upload" class="max-md:space-y-6 md:flex md:flex-wrap">
        <div class="space-y-6 md:w-1/2 md:pr-5 md:flex md:flex-col md:justify-between">
            <div>
                <InputLabel for="receipt_upload" value="File Upload" />
                <FileInput class="mt-1 block w-full" v-model="form.receipt_upload" />
                <InputError class="mt-2" :message="form.errors.receipt_upload" />
            </div>
        </div>

        <div class="flex items-center gap-4 w-full mt-10">
            <PrimaryButton :disabled="form.processing">Upload</PrimaryButton>
        </div>
    </form>
</template>

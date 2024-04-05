<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { useAlertStore } from '@/Stores/alert';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    expense: Object,
    receipt: Object,
});

const alert = useAlertStore();

const processing = ref(false);
const errorMessage = ref('');

const deleteReceipt = () => {
    processing.value = true;

    router.delete(route('expenses.receipts.delete', { expense: props.expense.id, receipt: props.receipt.id }), {
        preserveScroll: true,
        onSuccess: (resp) => {
            alert.setMessage(resp.props.flash.message);
            errorMessage.value = '';
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
    <form @submit.prevent="deleteReceipt">
        <div
            class="flex items-center justify-center rounded-lg border border-dashed border-gray-400 p-10 md:w-1/2 md:p-5">
            <img :src="'data:image;base64,' + receipt.image_contents" alt="" />
        </div>
        <InputError class="mt-2" :message="errorMessage" />
        <div class="flex items-center gap-4 w-full mt-10">
            <DangerButton :disabled="processing">Delete</DangerButton>
        </div>
    </form>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { useAlertStore } from '@/Stores/alert';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    expense: Object,
    receipt: Object,
});

const show = defineModel({ type: Boolean, default: false });

const form = useForm({
    password: '',
});

const close = () => {
    show.value = false;
    form.reset();
    form.clearErrors();
};

const alert = useAlertStore();

const submit = () => {
    form.delete(route('expenses.receipts.delete', { expense: props.expense.id, receipt: props.receipt.id }), {
        preserveScroll: true,
        onSuccess: (resp) => {
            alert.setMessage(resp.props.flash.message);
            close();
        },
        onError: (err) => {
            console.log('errors', err);
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="md" @close="close">
        <template #header>Are you sure you want to delete this receipt?</template>

        <template #description>
            <p class="text-sm font-bold text-gray-600">{{ receipt.mimetype }}, {{ receipt.size_formatted }}</p>
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
                    @keyup.enter="submit" />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>
        </template>

        <div class="mt-6 flex justify-end">
            <SecondaryButton @click="close">Cancel</SecondaryButton>
            <DangerButton class="ms-3" @click="submit">Delete Receipt</DangerButton>
        </div>
    </Modal>
</template>

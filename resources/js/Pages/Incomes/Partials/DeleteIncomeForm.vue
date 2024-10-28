<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useAlertStore } from '@/Stores/alert';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    income: Object,
});

const form = useForm({
    password: '',
});

const showConfirmModal = ref(false);

const alert = useAlertStore();

const deleteIncome = () => {
    form.delete(route('incomes.delete', props.income.id), {
        preserveScroll: true,
        onSuccess: (resp) => {
            closeModal();
            alert.setSuccessMessage(resp.props.flash.message, resp.props.flash.title);
        },
        onError: (err) => {
            console.log('income delete error', err);
        },
    });
};

const closeModal = () => {
    showConfirmModal.value = false;
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete Income</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                This is a permanent action and cannot be undone.
            </p>
        </header>

        <DangerButton @click="showConfirmModal = true">Delete Income</DangerButton>

        <Modal :show="showConfirmModal" max-width="md" @close="closeModal">
            <template #header> Are you sure you want to delete this income? </template>

            <template #description>
                <p class="text-sm font-bold text-gray-600">{{ income.source }}, {{ income.amount_pretty }}</p>
                <p class="mt-3 text-sm text-gray-600">
                    Please enter your password to confirm you would like to permanently delete this income.
                </p>
                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        placeholder="Password"
                        @keyup.enter="deleteIncome" />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>
            </template>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                <DangerButton class="ms-3" @click="deleteIncome"> Delete Income </DangerButton>
            </div>
        </Modal>
    </section>
</template>

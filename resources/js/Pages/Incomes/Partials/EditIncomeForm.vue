<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import NumberInput from '@/Components/Forms/NumberInput.vue';
import Textarea from '@/Components/Forms/Textarea.vue';
import { useForm } from '@inertiajs/vue3';
import { useAlertStore } from '@/Stores/alert';

const props = defineProps({
    income: Object,
});

const form = useForm({
    source: props.income.source,
    amount: props.income.amount / 100,
    payment_date: props.income.payment_date,
    effective_date: props.income.effective_date,
    notes: props.income.notes,
});

const alert = useAlertStore();

const update = () => {
    form.patch(route('incomes.update', props.income.id), {
        preserveScroll: true,
        onSuccess: (resp) => {
            alert.setMessage(resp.props.flash.message);
        },
        onError: () => {
            console.log('errors', form.errors);
        },
    });
};
</script>

<template>
    <form @submit.prevent="update">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-lg font-medium text-gray-900">Income Information</h2>
                <p class="mt-1 text-sm text-gray-600">All fields in the section below are required.</p>

                <div class="mt-10 grid grid-cols-1 gap-x-10 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <InputLabel for="source" value="Source" />
                        <TextInput
                            id="source"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.source"
                            required
                            autofocus />
                        <InputError class="mt-2" :message="form.errors.source" />
                    </div>
                    <div class="sm:col-span-3">
                        <InputLabel for="amount" value="Amount" />
                        <NumberInput
                            id="amount"
                            class="mt-1 block w-full"
                            step=".01"
                            placeholder="0.00"
                            v-model="form.amount"
                            required />
                        <InputError class="mt-2" :message="form.errors.amount" />
                    </div>
                    <div class="sm:col-span-3">
                        <InputLabel for="payment_date" value="Payment Date" />
                        <TextInput
                            id="payment_date"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.payment_date"
                            required />
                        <InputError class="mt-2" :message="form.errors.payment_date" />
                    </div>
                    <div class="sm:col-span-3">
                        <InputLabel for="effective_date" value="Effective Date" />
                        <TextInput
                            id="effective_date"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.effective_date"
                            required />
                        <InputError class="mt-2" :message="form.errors.effective_date" />
                    </div>
                    <div class="sm:col-span-3">
                        <InputLabel for="notes" value="Notes" />
                        <Textarea id="notes" class="mt-1 block w-full" rows="5" v-model="form.notes" />
                        <InputError class="mt-2" :message="form.errors.notes" />
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-start w-full mt-10">
            <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
        </div>
    </form>
</template>

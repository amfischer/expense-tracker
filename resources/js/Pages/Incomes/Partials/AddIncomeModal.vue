<script setup>
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from '@headlessui/vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import NumberInput from '@/Components/Forms/NumberInput.vue';
import Textarea from '@/Components/Forms/Textarea.vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { useAlertStore } from '@/Stores/alert';

const show = defineModel({
    type: Boolean,
    default: false,
});

const form = useForm({
    source: '',
    amount: '',
    payment_date: '',
    effective_date: '',
    is_earned_income: false,
    notes: '',
});

const alert = useAlertStore();

const create = () => {
    form.post(route('incomes.store'), {
        preserveScroll: true,
        onSuccess: (resp) => {
            show.value = false;
            form.reset();
            alert.setSuccessMessage(resp.props.flash.message, resp.props.flash.title);
        },
        onError: (errors) => {
            console.error('errors', errors);
        },
    });
};

const cancel = () => {
    show.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Modal :show="show" max-width="xl" @close="show = false">
        <template #header>
            <span>Add Income</span>
        </template>
        <form @submit.prevent="create">
            <div class="space-y-6">
                <div class="border-b border-gray-900/10 pb-6">
                    <h4 class="mt-8 text-lg text-gray-900">Required Fields</h4>

                    <div class="mt-6">
                        <div class="mb-6">
                            <div class="sm:flex sm:items-center">
                                <InputLabel for="source" value="Source" class="grow" />
                                <TextInput
                                    id="source"
                                    type="text"
                                    class="block w-full mt-1 sm:w-[300px] sm:mt-0"
                                    v-model="form.source"
                                    required
                                    autofocus />
                            </div>
                            <InputError class="mt-2 w-full sm:w-[300px] sm:ml-auto" :message="form.errors.source" />
                        </div>
                        <div class="mb-6">
                            <div class="sm:flex sm:items-center">
                                <InputLabel for="amount" value="Amount" class="grow" />
                                <NumberInput
                                    id="amount"
                                    class="block w-full mt-1 sm:w-[300px] sm:mt-0"
                                    step=".01"
                                    placeholder="0.00"
                                    v-model="form.amount"
                                    required />
                            </div>
                            <InputError class="mt-2 w-full sm:w-[300px] sm:ml-auto" :message="form.errors.amount" />
                        </div>
                        <div class="mb-6">
                            <div class="sm:flex sm:items-center">
                                <InputLabel for="payment_date" value="Payment Date" class="grow" />
                                <TextInput
                                    id="payment_date"
                                    type="date"
                                    class="block w-full mt-1 sm:w-[300px] sm:mt-0"
                                    v-model="form.payment_date"
                                    required />
                            </div>
                            <InputError
                                class="mt-2 w-full sm:w-[300px] sm:ml-auto"
                                :message="form.errors.payment_date" />
                        </div>
                        <div class="mb-6">
                            <div class="sm:flex sm:items-center">
                                <InputLabel for="effective_date" value="Effective Date" class="grow" />
                                <TextInput
                                    id="effective_date"
                                    type="date"
                                    class="block w-full mt-1 sm:w-[300px] sm:mt-0"
                                    v-model="form.effective_date"
                                    required />
                            </div>
                            <InputError
                                class="mt-2 w-full sm:w-[300px] sm:ml-auto"
                                :message="form.errors.effective_date" />
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg text-gray-900">Optional Fields</h4>

                    <div class="mt-6 space-y-6">
                        <SwitchGroup as="div" class="flex items-center justify-between">
                            <span class="flex grow flex-col">
                                <SwitchLabel as="span" class="text-sm font-medium text-gray-700" passive>
                                    Earned Income
                                </SwitchLabel>
                                <SwitchDescription as="span" class="text-sm text-gray-500">
                                    Track earned income for tax purposes & reporting.
                                </SwitchDescription>
                            </span>
                            <Switch
                                v-model="form.is_earned_income"
                                :class="[
                                    form.is_earned_income ? 'bg-indigo-600' : 'bg-gray-200',
                                    'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                                ]">
                                <span
                                    aria-hidden="true"
                                    :class="[
                                        form.is_earned_income ? 'translate-x-5' : 'translate-x-0',
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out',
                                    ]" />
                            </Switch>
                        </SwitchGroup>
                        <InputError class="mt-2" :message="form.errors.is_earned_income" />
                        <div>
                            <InputLabel for="notes" value="Notes" />
                            <Textarea id="notes" class="mt-1 block w-full" rows="5" v-model="form.notes" />
                            <InputError class="mt-2" :message="form.errors.notes" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-4 w-full mt-10">
                <SecondaryButton :disabled="form.processing" @click="cancel">Cancel</SecondaryButton>
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
            </div>
        </form>
    </Modal>
</template>

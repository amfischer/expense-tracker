<script setup>
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from '@headlessui/vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonBack from '@/Components/Buttons/ButtonBack.vue';
import TextInput from '@/Components/TextInput.vue';
import NumberInput from '@/Components/Forms/NumberInput.vue';
import Textarea from '@/Components/Forms/Textarea.vue';
import SelectMenu from '@/Components/Forms/SelectMenu.vue';
import SelectMenuBasic from '@/Components/Forms/SelectMenuBasic.vue';
import { useAlertStore } from '@/Stores/alert';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    expense: Object,
    categories: Array,
    currencies: Array,
    paymentMethods: Array,
});

const form = useForm({
    payee: props.expense.payee,
    amount: props.expense.amount / 100,
    is_business_expense: props.expense.is_business_expense,
    currency: props.expense.currency,
    payment_method: props.expense.payment_method ?? '',
    transaction_date: props.expense.transaction_date,
    effective_date: props.expense.effective_date,
    category_id: props.expense.category_id,
    notes: props.expense.notes,
});

const alert = useAlertStore();

const update = () => {
    form.patch(route('expenses.update', props.expense.id), {
        preserveScroll: true,
        onSuccess: (resp) => {
            alert.setSuccessMessage(resp.props.flash.message, resp.props.flash.title);
        },
        onError: () => {
            console.error('errors', form.errors);
        },
    });
};
</script>

<template>
    <form @submit.prevent="update">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <div class="sm:flex sm:items-start">
                    <div class="sm:flex-auto">
                        <h2 class="text-lg font-medium text-gray-900">Expense Information</h2>
                        <p class="mt-1 text-sm text-gray-600">All fields in the section below are required.</p>
                    </div>
                    <div class="mt-3 sm:mt-0 sm:flex-none">
                        <ButtonBack />
                    </div>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-x-10 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <InputLabel for="payee" value="Payee" />
                        <TextInput
                            id="payee"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.payee"
                            required
                            autofocus />
                        <InputError class="mt-2" :message="form.errors.payee" />
                    </div>
                    <div class="sm:col-span-3">
                        <InputLabel for="category" value="Category" />
                        <SelectMenu :options="categories" v-model="form.category_id" />
                        <InputError class="mt-2" :message="form.errors.category_id" />
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
                        <InputLabel for="currency" value="Currency" />
                        <SelectMenuBasic :options="currencies" v-model="form.currency" />
                        <InputError class="mt-2" :message="form.errors.currency" />
                    </div>

                    <div class="sm:col-span-3">
                        <InputLabel for="transaction_date" value="Transaction Date" />
                        <TextInput
                            id="transaction_date"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.transaction_date"
                            required />
                        <InputError class="mt-2" :message="form.errors.transaction_date" />
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
                </div>
            </div>

            <div>
                <h2 class="text-lg font-medium text-gray-900">Additional Options</h2>
                <p class="mt-1 text-sm text-gray-600">All fields in the section below are optional.</p>

                <div class="mt-10 space-y-10 md:w-1/2 md:pr-5">
                    <div class="sm:col-span-3">
                        <InputLabel for="payment_method" value="Payment Method" />
                        <SelectMenuBasic :options="paymentMethods" v-model="form.payment_method" />
                        <InputError class="mt-2" :message="form.errors.payment_method" />
                    </div>

                    <SwitchGroup as="div" class="flex items-center justify-between">
                        <span class="flex flex-grow flex-col">
                            <SwitchLabel as="span" class="text-sm font-medium text-gray-700" passive>
                                Business Expense
                            </SwitchLabel>
                            <SwitchDescription as="span" class="text-sm text-gray-500">
                                Track business expenses for tax purposes & reporting.
                            </SwitchDescription>
                        </span>
                        <Switch
                            v-model="form.is_business_expense"
                            :class="[
                                form.is_business_expense ? 'bg-indigo-600' : 'bg-gray-200',
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                            ]">
                            <span
                                aria-hidden="true"
                                :class="[
                                    form.is_business_expense ? 'translate-x-5' : 'translate-x-0',
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                ]" />
                        </Switch>
                    </SwitchGroup>
                    <InputError class="mt-2" :message="form.errors.is_business_expense" />

                    <div>
                        <InputLabel for="notes" value="Notes" />
                        <Textarea id="notes" class="mt-1 block w-full" rows="5" v-model="form.notes" />
                        <InputError class="mt-2" :message="form.errors.notes" />
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center w-full mt-10">
            <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
        </div>
    </form>
</template>

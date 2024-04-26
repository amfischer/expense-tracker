<script setup>
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from '@headlessui/vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import NumberInput from '@/Components/Forms/NumberInput.vue';
import Textarea from '@/Components/Forms/Textarea.vue';
import SelectMenu from '@/Components/Forms/SelectMenu.vue';
import SelectMenuBasic from '@/Components/Forms/SelectMenuBasic.vue';
import { useForm } from '@inertiajs/vue3';
import { useAlertStore } from '@/Stores/alert';

const props = defineProps({
    categories: Array,
    currencies: Array,
    paymentMethods: Object,
});

const form = useForm({
    payee: '',
    amount: '',
    foreign_currency_conversion_fee: '',
    currency: props.currencies[0],
    payment_method: '',
    is_business_expense: false,
    transaction_date: '',
    effective_date: '',
    category_id: props.categories[0].id,
    notes: '',
});

const alert = useAlertStore();

const create = () => {
    form.post(route('expenses.store'), {
        preserveScroll: true,
        onSuccess: (resp) => {
            form.reset();
            alert.setMessage(resp.props.flash.message);
        },
        onError: () => {
            console.log('errors', form.errors);
        },
    });
};
</script>

<template>
    <form @submit.prevent="create">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-lg font-medium text-gray-900">Expense Information</h2>
                <p class="mt-1 text-sm text-gray-600">All fields in the section below are required.</p>

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
                        <InputLabel for="date" value="Transaction Date" />
                        <TextInput
                            id="date"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.transaction_date"
                            required />
                        <InputError class="mt-2" :message="form.errors.transaction_date" />
                    </div>
                    <div class="sm:col-span-3">
                        <InputLabel for="date" value="Effective Date" />
                        <TextInput
                            id="date"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.effective_date"
                            required />
                        <InputError class="mt-2" :message="form.errors.effective_date" />
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-lg font-medium text-gray-900">Additional Options</h2>
                <p class="mt-1 text-sm text-gray-600">All fields in the section below are optional.</p>

                <div class="mt-10 space-y-10 md:w-1/2 md:pr-5">
                    <div class="sm:col-span-3">
                        <InputLabel for="payment_method" value="Payment Method" />
                        <SelectMenuBasic :options="paymentMethods" :has-keys="true" v-model="form.payment_method" />
                        <InputError class="mt-2" :message="form.errors.payment_method" />
                    </div>

                    <div>
                        <InputLabel for="foreign_currency_conversion_fee" value="Foreign Currency Conversion Fee" />
                        <NumberInput
                            id="foreign_currency_conversion_fee"
                            class="mt-1 block w-full"
                            step=".01"
                            placeholder="0.00"
                            v-model="form.foreign_currency_conversion_fee" />
                        <InputError class="mt-2" :message="form.errors.foreign_currency_conversion_fee" />
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
        <div class="flex items-center justify-end gap-4 w-full mt-10">
            <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
        </div>
    </form>
</template>

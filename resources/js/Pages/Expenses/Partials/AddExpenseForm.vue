<script setup>
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
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    categories: Array,
    currencies: Array,
});

const form = useForm({
    payee: '',
    amount: 0,
    foreign_currency_conversion_fee: 0,
    currency: props.currencies[0],
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
            form.reset()
            alert.setMessage(resp.props.flash.message)
        },
        onError: () => {
            console.log('errors', form.errors)
        },
    });
};

</script>

<template>
    <form @submit.prevent="create" class="max-md:space-y-6 md:flex md:flex-wrap">
        <div class="space-y-6 md:w-1/2 md:pr-5 md:flex md:flex-col md:justify-between">
            <div>
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

            <div>
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

            <div>
                <InputLabel for="currency" value="Currency" />
                <SelectMenuBasic :options="currencies" v-model="form.currency" />
                <InputError class="mt-2" :message="form.errors.currency" />
            </div>

            <div>
                <InputLabel for="date" value="Transaction Date" />
                <TextInput
                    id="date"
                    type="date"
                    class="mt-1 block w-full"
                    v-model="form.transaction_date"
                    required />
                <InputError class="mt-2" :message="form.errors.transaction_date" />
            </div>

            <div>
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

        <div class="space-y-6 md:w-1/2 md:pl-5 md:flex md:flex-col md:justify-between">
            <div>
                <InputLabel for="category" value="Category" />
                <SelectMenu :options="categories" v-model="form.category_id" />
                <InputError class="mt-2" :message="form.errors.category_id" />
            </div>

            <div class="flex items-center mb-2 md:w-1/2">
                <div class="flex h-6 items-center">
                    <Checkbox id="business_expense" v-model:checked="form.is_business_expense" />
                </div>
                <div class="ml-3">
                    <InputLabel for="business_expense" value="Business Expense" class="cursor-pointer" />
                </div>
            </div>
            <InputError class="mt-2" :message="form.errors.is_business_expense" />

            <div>
                <InputLabel for="notes" value="Notes" />
                <Textarea
                    id="notes"
                    class="mt-1 block w-full"
                    rows="5"
                    v-model="form.notes" />
                <InputError class="mt-2" :message="form.errors.notes" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 w-full mt-10">
            <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
        </div>
    </form>
</template>

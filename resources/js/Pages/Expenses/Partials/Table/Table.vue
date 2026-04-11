<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import {
    FunnelIcon,
    TagIcon,
    CurrencyDollarIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    InformationCircleIcon,
} from '@heroicons/vue/20/solid';

import SearchBox from '@/Components/Tables/SearchBox.vue';
import TableHeader from '@/Components/Tables/TableHeader.vue';
import FilterDialog from '@/Components/Tables/FilterDialog.vue';
import FilterOption from '@/Components/Tables/FilterOption.vue';
import MiscFilters from '@/Components/Tables/MiscFilters.vue';
import DatePicker from '@/Components/Tables/DatePicker.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import ShowReceiptModal from '../ShowReceiptModal.vue';
import AddExpenseModal from '../AddExpenseModal.vue';

import { useScoutHttpGet } from '@/Composables/scoutHttpGet';
import { useDateFormatter } from '@/Composables/dateFormatter';
import { Link } from '@inertiajs/vue3';
import { inject, reactive, ref } from 'vue';

defineProps({
    expenses: Object,
});

const scout = reactive(useScoutHttpGet({ url: route('expenses.index') }));

const { df } = useDateFormatter();

const categories = inject('categories');
const paymentMethods = inject('paymentMethods');

const pm = paymentMethods.reduce((obj, method) => {
    obj[method.id] = method.name;
    return obj;
}, {});

const showFilters = ref(false);

const showCreateModal = ref(false);
const copiedProperties = ref({});
const createCopy = (expense) => {
    copiedProperties.value = {
        payee: expense.payee,
        category_id: expense.category_id,
        amount: expense.amount / 100,
        payment_method: expense.payment_method,
        is_business_expense: expense.is_business_expense,
        notes: expense.notes,
    };

    showCreateModal.value = true;
};

const showReceipt = ref(false);
const selectedExpense = ref(null);
const selectedReceipt = ref(null);
const toggleReceiptModal = (expense, receipt) => {
    selectedExpense.value = expense;
    selectedReceipt.value = receipt;
    showReceipt.value = true;
};
</script>

<template>
    <div class="mb-10 sm:flex sm:items-start">
        <div class="sm:flex-auto">
            <h1 class="text-xl leading-6 font-semibold text-gray-900">Expenses</h1>
            <p class="mt-2 text-sm text-gray-700">A searchable and filterable table of all recorded expenses.</p>
        </div>
        <div class="mt-3 sm:mt-0 sm:flex-none">
            <PrimaryButton @click="showCreateModal = true">Add expense</PrimaryButton>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="mb-10 flex items-center justify-between gap-3">
        <SearchBox v-model="scout.form.query" @keyup="scout.search" @reset="scout.clearQuery" />
        <button type="button" class="text-sm font-medium text-gray-400 hover:text-gray-500" @click="showFilters = true">
            <span class="sr-only">Filters</span>
            <FunnelIcon class="h-5 w-5" aria-hidden="true" />
        </button>
    </div>

    <table class="min-w-full divide-y divide-gray-300 bg-white">
        <thead class="hidden lg:table-header-group">
            <tr>
                <th scope="col" class="relative w-14 p-4">
                    <span class="sr-only">Toggle Information</span>
                </th>
                <TableHeader title="Payee" field="payee" :scout="scout" />
                <TableHeader title="Amount" field="amount" width="lg:w-36" :scout="scout" />
                <TableHeader title="Date" field="effective_date" width="lg:w-36" :scout="scout" />
                <th scope="col" class="relative w-14 p-4">
                    <span class="sr-only">Edit</span>
                </th>
                <th scope="col" class="relative w-14 p-4">
                    <span class="sr-only">Copy</span>
                </th>
            </tr>
        </thead>

        <tbody v-if="expenses.data.length === 0" class="divide-y divide-gray-200">
            <tr>
                <td></td>
                <td colspan="4" class="text-md py-3 text-gray-500">No expense data found.</td>
                <td></td>
            </tr>
        </tbody>

        <tbody v-else class="divide-y divide-y-reverse divide-gray-200">
            <template v-for="expense in expenses.data" :key="expense.id">
                <Disclosure v-slot="{ open }">
                    <tr class="table-tr-hover">
                        <td class="w-10 py-3 text-center sm:w-14">
                            <DisclosureButton class="rounded-sm border border-gray-400" :class="open && 'open'">
                                <ChevronUpIcon class="h-5 w-5 text-gray-600" v-if="open" />
                                <ChevronDownIcon class="h-5 w-5 text-gray-600" v-else />
                            </DisclosureButton>
                        </td>
                        <td class="text-md py-3 align-baseline text-gray-500 lg:align-middle">
                            <div class="flex flex-row items-baseline gap-2">
                                {{ expense.payee }}
                                <div class="flex items-center gap-2">
                                    <InformationCircleIcon v-if="expense.notes" class="h-3 w-3 text-blue-400" />
                                    <TagIcon v-if="expense.receipts.length > 0" class="h-3 w-3 text-gray-400" />
                                    <CurrencyDollarIcon
                                        v-if="expense.is_business_expense"
                                        class="h-3 w-3 text-green-700" />
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-sm">
                                <span
                                    class="block h-2 w-2 rounded-full"
                                    :style="{ backgroundColor: expense.category.color }">
                                </span>
                                {{ expense.category.name }}
                            </div>
                        </td>
                        <td class="w-[100px] py-3 align-baseline text-sm text-gray-500 lg:align-middle">
                            <span class="text-md font-bold lg:text-sm lg:font-normal">
                                {{ expense.amount_pretty }}
                            </span>
                            <div class="lg:hidden">
                                {{ expense.effective_date_pretty }}
                            </div>
                        </td>
                        <td class="hidden py-3 text-sm text-gray-500 lg:table-cell">
                            {{ expense.effective_date_pretty }}
                        </td>
                        <td
                            class="hidden py-3 text-sm font-medium sm:table-cell sm:align-baseline md:w-14 lg:align-middle">
                            <Link
                                :href="route('expenses.edit', expense.id)"
                                class="text-indigo-600 hover:text-indigo-900">
                                <span class="inline-block hover:underline">Edit</span>
                            </Link>
                        </td>
                        <td
                            class="hidden py-3 text-sm font-medium sm:table-cell sm:align-baseline md:w-14 lg:align-middle">
                            <button
                                class="rounded-sm border border-gray-300 px-2 text-xs leading-tight text-gray-500 hover:bg-gray-600 hover:text-white"
                                @click="createCopy(expense)">
                                Copy
                            </button>
                        </td>
                    </tr>
                    <tr class="border-none">
                        <td></td>
                        <td>
                            <transition name="slide-down">
                                <DisclosurePanel as="dl" class="">
                                    <div class="pb-3">
                                        <div class="grid grid-cols-table-dl gap-4 pb-1">
                                            <dt class="text-xs text-gray-800">Payment Method</dt>
                                            <dd class="text-sm leading-4">
                                                {{ pm[expense.payment_method] }}
                                            </dd>
                                        </div>
                                        <div class="grid grid-cols-table-dl gap-4 pb-1">
                                            <dt class="text-xs text-gray-800">Transaction Date</dt>
                                            <dd class="text-sm leading-4">
                                                {{ df(expense.transaction_date) }}
                                            </dd>
                                        </div>
                                        <div class="grid grid-cols-table-dl gap-4 pb-1">
                                            <dt class="text-xs text-gray-800">Effective Date</dt>
                                            <dd class="text-sm leading-4">
                                                {{ df(expense.effective_date) }}
                                            </dd>
                                        </div>
                                    </div>
                                    <div class="pb-3" v-if="expense.receipts.length > 0">
                                        <div
                                            v-for="(receipt, index) in expense.receipts"
                                            :key="receipt.id"
                                            class="grid grid-cols-table-dl gap-4 pb-1">
                                            <dt class="text-xs text-gray-800">
                                                Receipt{{ expense.receipts.length > 1 ? ` ${index + 1}` : '' }}
                                            </dt>
                                            <dd class="text-xs leading-4">
                                                <button
                                                    class="rounded-sm border border-indigo-600 bg-indigo-600 px-2 text-white hover:bg-indigo-900"
                                                    @click="toggleReceiptModal(expense, receipt)">
                                                    Show
                                                </button>
                                            </dd>
                                        </div>
                                    </div>
                                    <div class="pb-3 sm:grid sm:grid-cols-table-dl sm:gap-4" v-show="expense.notes">
                                        <dt class="pb-1 text-xs text-gray-800">Notes</dt>
                                        <dd
                                            class="markdown-field text-sm leading-4 text-gray-800"
                                            v-html="expense.notes_html"></dd>
                                    </div>
                                </DisclosurePanel>
                            </transition>
                        </td>
                        <td colspan="4" class="align-baseline">
                            <DisclosurePanel class="leading-4 sm:hidden">
                                <Link
                                    :href="route('expenses.edit', expense.id)"
                                    class="mb-6 block text-sm leading-4 text-indigo-600 underline hover:text-indigo-900">
                                    Edit
                                </Link>
                                <button
                                    class="rounded-sm border border-gray-300 px-2 text-xs leading-tight text-gray-500 hover:bg-gray-600 hover:text-white"
                                    @click="createCopy(expense)">
                                    Copy
                                </button>
                            </DisclosurePanel>
                        </td>
                    </tr>
                </Disclosure>
            </template>
        </tbody>
    </table>

    <Pagination :paginator="expenses" />

    <AddExpenseModal v-model:show="showCreateModal" v-model:copy="copiedProperties" />

    <FilterDialog :open="showFilters" :scout="scout" @close="showFilters = false">
        <FilterOption title="Categories" field="category_ids" :options="categories" :scout="scout" />
        <FilterOption title="Payment Methods" field="payment_methods" :options="paymentMethods" :scout="scout" />
        <MiscFilters :scout="scout" />
        <DatePicker :scout="scout" />
    </FilterDialog>
    <ShowReceiptModal v-model="showReceipt" :expense="selectedExpense" :receipt="selectedReceipt" />
</template>

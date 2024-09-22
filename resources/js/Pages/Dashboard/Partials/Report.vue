<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import Container from '@/Components/Container.vue';
import WhiteCard from '@/Components/WhiteCard.vue';
import BarChart from './BarChart.vue';
import CategoryMenu from './CategoryMenu.vue';
import {
    CurrencyDollarIcon,
    TagIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    InformationCircleIcon,
} from '@heroicons/vue/20/solid';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useDateFormatter } from '@/Composables/dateFormatter';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    categories: {
        type: Array,
        default: [],
    },
    paymentMethods: Array,
});

const selectedCategory = ref(props.categories[0]);

const { df } = useDateFormatter();

const pm = props.paymentMethods.reduce((obj, method) => {
    obj[method.id] = method.name;
    return obj;
}, {});

const showReceiptModal = ref(false);
const selectedReciept = ref(null);
const toggleReceiptModal = (expense) => {
    selectedReciept.value = expense.receipts[0];
    selectedReciept.value.src = route('expenses.receipts.show', {
        expense: expense.id,
        receipt: expense.receipts[0].id,
    });
    showReceiptModal.value = true;
};
</script>

<template>
    <Container class="py-12 hidden md:block">
        <WhiteCard>
            <BarChart :label="label" :categories="categories" />
        </WhiteCard>
    </Container>

    <Container class="py-12">
        <WhiteCard class="flex-1">
            <div class="flex items-baseline justify-between mb-10">
                <div>
                    <h3 class="text-xl font-semibold leading-6 text-gray-900">Category Details</h3>
                    <p class="mt-2 text-sm text-gray-700">Select a category to see underlying expenses.</p>
                </div>

                <CategoryMenu :categories="categories" v-model="selectedCategory" />
            </div>

            <table class="min-w-full divide-y divide-gray-300 bg-white">
                <thead class="hidden lg:table-header-group">
                    <tr>
                        <th scope="col" class="relative p-4 w-14">
                            <span class="sr-only">Toggle Information</span>
                        </th>
                        <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Payee</th>
                        <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-36">Amount</th>
                        <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-36">Date</th>
                        <th scope="col" class="relative p-4 w-14">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>

                <tbody v-if="selectedCategory === null" class="divide-y divide-gray-200">
                    <tr>
                        <td></td>
                        <td colspan="4" class="py-3 text-md text-gray-500">No expense data found.</td>
                    </tr>
                </tbody>

                <tbody v-else class="divide-y divide-gray-200">
                    <template v-for="expense in selectedCategory.expenses" :key="expense.id">
                        <Disclosure v-slot="{ open }">
                            <tr class="table-tr-hover">
                                <td class="py-3 text-center w-10 sm:w-14">
                                    <DisclosureButton class="border border-gray-400 rounded" :class="open && 'open'">
                                        <ChevronUpIcon class="h-5 w-5 text-gray-600" v-if="open" />
                                        <ChevronDownIcon class="h-5 w-5 text-gray-600" v-else />
                                    </DisclosureButton>
                                </td>
                                <td class="py-3 text-md text-gray-500 align-baseline lg:align-middle">
                                    <div class="flex flex-row items-baseline gap-2">
                                        {{ expense.payee }}
                                        <div class="flex items-center gap-2">
                                            <InformationCircleIcon
                                                v-if="expense.notes !== ''"
                                                class="h-3 w-3 text-blue-400" />
                                            <TagIcon v-if="expense.has_receipt" class="h-3 w-3 text-gray-400" />
                                            <CurrencyDollarIcon
                                                v-if="expense.is_business_expense"
                                                class="h-3 w-3 text-green-700" />
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 text-sm">
                                        <span
                                            class="rounded-full block w-2 h-2"
                                            :style="{ backgroundColor: expense.category.color }">
                                        </span>
                                        {{ expense.category.name }}
                                    </div>
                                </td>
                                <td class="py-3 w-[100px] text-sm text-gray-500 align-baseline lg:align-middle">
                                    <span
                                        :class="{ 'underline underline-offset-2 decoration-dotted': expense.has_fees }"
                                        class="font-bold text-md lg:text-sm lg:font-normal">
                                        {{ expense.amount_pretty }}
                                    </span>
                                    <div class="lg:hidden">
                                        {{ expense.effective_date_pretty }}
                                    </div>
                                </td>
                                <td class="hidden lg:table-cell py-3 text-sm text-gray-500">
                                    {{ expense.effective_date_pretty }}
                                </td>
                                <td
                                    class="hidden sm:table-cell sm:align-baseline lg:align-middle py-3 text-sm font-medium md:w-14">
                                    <Link
                                        :href="route('expenses.edit', expense.id)"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        <span class="inline-block hover:underline">Edit</span>
                                    </Link>
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
                                            <div class="pb-3" v-if="expense.has_receipt">
                                                <div class="grid grid-cols-table-dl gap-4 pb-1">
                                                    <dt class="text-xs text-gray-800">Reciept</dt>
                                                    <dd class="text-xs leading-4">
                                                        <button
                                                            class="border rounded border-indigo-600 bg-indigo-600 hover:bg-indigo-900 text-white px-2"
                                                            @click="toggleReceiptModal(expense)">
                                                            Show
                                                        </button>
                                                    </dd>
                                                </div>
                                            </div>
                                            <div
                                                class="pb-3 sm:grid sm:grid-cols-table-dl sm:gap-4"
                                                v-show="expense.notes !== ''">
                                                <dt class="text-xs text-gray-800 pb-1">Notes</dt>
                                                <dd
                                                    class="text-sm text-gray-800 leading-4 markdown-field"
                                                    v-html="expense.notes"></dd>
                                            </div>
                                        </DisclosurePanel>
                                    </transition>
                                </td>
                                <td colspan="3" class="align-baseline">
                                    <DisclosurePanel class="sm:hidden leading-4">
                                        <Link
                                            :href="route('expenses.edit', expense.id)"
                                            class="text-sm leading-4 underline text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </Link>
                                    </DisclosurePanel>
                                </td>
                            </tr>
                        </Disclosure>
                    </template>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="font-bold pt-10">Total</td>
                        <td class="font-bold pt-10">{{ selectedCategory.total }}</td>
                    </tr>
                </tfoot>
            </table>
        </WhiteCard>
    </Container>

    <Modal
        :show="showReceiptModal"
        @close="showReceiptModal = false"
        :max-width="selectedReciept?.is_image ? '2xl' : '6xl'"
        :dialog-panel-classes="selectedReciept?.is_image ? 'overflow-y-scroll p-0' : 'h-screen p-0'">
        <img v-if="selectedReciept?.is_image" :src="selectedReciept.src" />
        <iframe v-else :src="selectedReciept.src" width="100%" height="100%" />
    </Modal>
</template>

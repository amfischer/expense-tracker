<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import {
    FunnelIcon,
    TagIcon,
    CurrencyDollarIcon,
    PencilSquareIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    InformationCircleIcon,
} from '@heroicons/vue/20/solid';
import ButtonLink from '@/Components/Buttons/ButtonLink.vue';
import Pagination from '@/Components/Pagination.vue';
import FilterDialog from './FilterDialog.vue';
import SearchBox from './SearchBox.vue';
import SortByMenu from './SortByMenu.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useScoutStore } from '@/Stores/scout';
import { useDateFormatter } from '@/Composables/dateFormatter';
import { onMounted, ref } from 'vue';

defineProps({
    expenses: Object,
});

const scout = useScoutStore();

const { df } = useDateFormatter();

onMounted(() => {
    let params = new URLSearchParams(document.location.search);
    scout.form.query = params.get('query') || '';
    scout.form.sort_by = params.get('sort_by') || 'effective_date';

    if (usePage().props.errors.scout !== undefined) {
        console.error('search errors: ', usePage().props.errors.scout);
    }
});

const showFilters = ref(false);

const pm = scout.options.paymentMethods.reduce((obj, method) => {
    obj[method.id] = method.name;
    return obj;
}, {});
</script>

<template>
    <div class="sm:flex sm:items-start mb-10">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold leading-6 text-gray-900">Expenses</h1>
            <p class="mt-2 text-sm text-gray-700">A searchable and filterable table of all recorded expenses.</p>
        </div>
        <div class="mt-3 sm:mt-0 sm:flex-none">
            <ButtonLink :href="route('expenses.create')">Add expense</ButtonLink>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="flex items-center justify-between gap-3 mb-10">
        <SearchBox v-model="scout.form.query" @keyup="scout.throttledSearch" @reset="scout.clearSearchQuery" />
        <div class="flex items-center gap-3 md:gap-8">
            <SortByMenu v-model="scout.form.sort_by" />
            <button
                type="button"
                class="inline-block text-sm font-medium text-gray-400 hover:text-gray-500"
                @click="showFilters = true">
                <span class="sr-only">Filters</span>
                <FunnelIcon class="h-5 w-5" aria-hidden="true" />
            </button>
        </div>
    </div>

    <table class="min-w-full divide-y divide-gray-300 bg-white">
        <thead class="hidden md:table-header-group">
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
        <tbody class="divide-y divide-gray-200">
            <template v-for="expense in expenses.data" :key="expense.id">
                <Disclosure v-slot="{ open }">
                    <tr class="table-tr-hover">
                        <td class="py-3 text-center w-10 sm:w-14">
                            <DisclosureButton class="border border-gray-400 rounded" :class="open && 'open'">
                                <ChevronUpIcon class="h-5 w-5 text-gray-600" v-if="open" />
                                <ChevronDownIcon class="h-5 w-5 text-gray-600" v-else />
                            </DisclosureButton>
                        </td>
                        <td class="py-3 text-md text-gray-500 align-baseline md:align-middle">
                            <div class="flex flex-row items-baseline gap-2">
                                {{ expense.payee }}
                                <div class="hidden sm:flex sm:items-center sm:gap-2">
                                    <InformationCircleIcon v-if="expense.notes !== ''" class="h-3 w-3 text-blue-400" />
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
                            <div class="flex items-center gap-1 pt-1 px-2 sm:hidden">
                                <InformationCircleIcon v-if="expense.notes !== ''" class="h-3 w-3 text-blue-400" />
                                <TagIcon v-if="expense.has_receipt" class="h-3 w-3 text-gray-400" />
                                <CurrencyDollarIcon v-if="expense.is_business_expense" class="h-3 w-3 text-green-700" />
                            </div>
                        </td>
                        <td class="py-3 text-sm text-gray-500 align-baseline md:align-middle">
                            <span
                                :class="{ 'underline underline-offset-2 decoration-dotted': expense.has_fees }"
                                class="font-bold text-md md:text-sm md:font-normal">
                                {{ expense.amount_pretty }}
                            </span>
                            <div class="md:hidden">
                                {{ expense.effective_date_pretty }}
                            </div>
                        </td>
                        <td class="hidden md:table-cell py-3 text-sm text-gray-500">
                            {{ expense.effective_date_pretty }}
                        </td>
                        <td class="hidden sm:table-cell py-3 text-sm font-medium md:w-14">
                            <Link
                                :href="route('expenses.edit', expense.id)"
                                class="text-indigo-600 hover:text-indigo-900">
                                <span class="inline-block hover:underline">Edit</span>
                            </Link>
                        </td>
                    </tr>
                    <tr class="border-none">
                        <td></td>
                        <td colspan="4">
                            <transition
                                enter-active-class="transition-all duration-300 ease-in overflow-hidden"
                                enter-from-class="transform max-h-0"
                                enter-to-class="transform max-h-96"
                                leave-active-class="transition-all duration-300 ease-out overflow-hidden"
                                leave-from-class="transform max-h-96"
                                leave-to-class="transform max-h-0">
                                <DisclosurePanel class="flex flex-col lg:flex-row lg:items-baseline lg:gap-16">
                                    <div class="pb-3">
                                        <p
                                            class="pb-1 text-xs text-gray-800 flex items-center justify-between gap-2 w-56">
                                            Payment Method:
                                            <span class="text-sm">
                                                {{ pm[expense.payment_method] ?? 'None' }}
                                            </span>
                                        </p>
                                        <p
                                            class="pb-1 text-xs text-gray-800 flex items-center justify-between gap-2 w-56">
                                            Transaction Date:
                                            <span class="text-sm">
                                                {{ df(expense.transaction_date) }}
                                            </span>
                                        </p>
                                        <p
                                            class="pb-1 text-xs text-gray-800 flex items-center justify-between gap-2 w-56">
                                            Effective Date:
                                            <span class="text-sm">
                                                {{ df(expense.effective_date) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div
                                        class="flex flex-col lg:flex-row lg:items-baseline lg:gap-2 pb-3"
                                        v-show="expense.notes !== ''">
                                        <p class="text-xs text-gray-800">Notes:</p>
                                        <p class="text-sm text-gray-800" v-html="expense.notes"></p>
                                    </div>
                                </DisclosurePanel>
                            </transition>
                        </td>
                        <td class="sm:hidden align-baseline">
                            <transition
                                enter-active-class="transition-all duration-300 ease-in overflow-hidden"
                                enter-from-class="transform max-h-0"
                                enter-to-class="transform max-h-96"
                                leave-active-class="transition-all duration-300 ease-out overflow-hidden"
                                leave-from-class="transform max-h-96"
                                leave-to-class="transform max-h-0">
                                <DisclosurePanel>
                                    <Link
                                        :href="route('expenses.edit', expense.id)"
                                        class="text-sm underline text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </Link>
                                </DisclosurePanel>
                            </transition>
                        </td>
                    </tr>
                </Disclosure>
            </template>
        </tbody>
    </table>

    <Pagination :paginator="expenses" />

    <FilterDialog :open="showFilters" @close="showFilters = false" />
</template>

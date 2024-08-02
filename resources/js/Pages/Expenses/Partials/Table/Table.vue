<script setup>
import ButtonLink from '@/Components/Buttons/ButtonLink.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchBox from './SearchBox.vue';
import SortByMenu from './SortByMenu.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useScoutStore } from '@/Stores/scout';
import { onMounted, ref } from 'vue';
import { FunnelIcon, TagIcon, CurrencyDollarIcon, PencilSquareIcon } from '@heroicons/vue/20/solid';
import FilterDialog from './FilterDialog.vue';
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';

defineProps({
    expenses: Object,
});

const scout = useScoutStore();

onMounted(() => {
    let params = new URLSearchParams(document.location.search);
    scout.form.query = params.get('query') || '';
    scout.form.sort_by = params.get('sort_by') || 'effective_date';

    if (usePage().props.errors.scout !== undefined) {
        console.error('search errors: ', usePage().props.errors.scout);
    }
});

const showFilters = ref(false);
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
                <th scope="col" class="relative p-4">
                    <span class="sr-only">Toggle Information</span>
                </th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Payee</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Amount</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Date</th>
                <th scope="col" class="relative p-4">
                    <span class="sr-only">Edit</span>
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            <template v-for="expense in expenses.data" :key="expense.id">
                <Disclosure v-slot="{ open }">
                    <tr class="hover:bg-gray-100">
                        <td class="py-3">
                            <DisclosureButton :class="open && 'open'">
                                <span class="dropdown-arrow"></span>
                            </DisclosureButton>
                        </td>
                        <td class="whitespace-nowrap py-3 text-md text-gray-500">
                            <div class="flex items-center gap-2">
                                <span
                                    :class="{ 'underline decoration-dotted underline-offset-2': expense.notes }"
                                    :title="expense.notes">
                                    {{ expense.payee }}
                                </span>
                                <TagIcon v-if="expense.has_receipt" class="h-3 w-3 text-gray-500" />
                                <CurrencyDollarIcon v-if="expense.is_business_expense" class="h-3 w-3 text-green-700" />
                            </div>
                            <div class="flex items-center gap-1 text-sm">
                                <span
                                    class="rounded-full block w-2 h-2"
                                    :style="{ backgroundColor: expense.category.color }">
                                </span>
                                {{ expense.category.name }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap py-3 text-sm text-gray-500">
                            <span
                                :title="
                                    expense.has_fees
                                        ? 'Foreign Currency Conversion Fee: ' +
                                          expense.foreign_currency_conversion_fee_pretty
                                        : ''
                                "
                                :class="{
                                    'underline underline-offset-2 decoration-dotted cursor-pointer': expense.has_fees,
                                }"
                                class="font-bold text-md md:text-sm md:font-normal">
                                {{ expense.amount_pretty }}
                            </span>

                            <div class="md:hidden">
                                {{ expense.effective_date_pretty }}
                            </div>
                        </td>
                        <td class="hidden md:table-cell whitespace-nowrap py-3 text-sm text-gray-500">
                            {{ expense.effective_date_pretty }}
                        </td>
                        <td class="whitespace-nowrap py-3 text-sm text-right font-medium">
                            <Link
                                :href="route('expenses.edit', expense.id)"
                                class="text-indigo-600 hover:text-indigo-900">
                                <span class="hidden md:inline-block">Edit</span>
                                <PencilSquareIcon class="h-4 w-4 md:hidden" aria-hidden="true" />
                            </Link>
                        </td>
                    </tr>
                    <tr class="border-none">
                        <td colspan="5">
                            <transition
                                enter-active-class="transition-all duration-300 ease-in overflow-hidden"
                                enter-from-class="transform max-h-0"
                                enter-to-class="transform max-h-96"
                                leave-active-class="transition-all duration-300 ease-out overflow-hidden"
                                leave-from-class="transform max-h-96"
                                leave-to-class="transform max-h-0">
                                <DisclosurePanel>
                                    <p v-html="expense.notes"></p>
                                    <p>lorem ipsum text</p>
                                    <p>lorem ipsum text</p>
                                    <p>lorem ipsum text</p>
                                    <p>lorem ipsum text</p>
                                    <p>lorem ipsum text</p>
                                    <p>lorem ipsum text</p>
                                    <p>lorem ipsum text</p>
                                    <p>lorem ipsum text</p>
                                    <p>lorem ipsum text</p>
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

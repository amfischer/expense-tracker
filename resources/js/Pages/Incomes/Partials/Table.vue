<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import {
    FunnelIcon,
    CurrencyDollarIcon,
    InformationCircleIcon,
    ChevronUpIcon,
    ChevronDownIcon,
} from '@heroicons/vue/20/solid';
import SearchBox from '@/Components/Tables/SearchBox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TableHeader from '@/Components/Tables/TableHeader.vue';
import FilterDialog from '@/Components/Tables/FilterDialog.vue';
import DatePicker from '@/Components/Tables/DatePicker.vue';
import Pagination from '@/Components/Pagination.vue';
import AddIncomeModal from './AddIncomeModal.vue';
import { useScoutHttpGet } from '@/Composables/scoutHttpGet';
import { useDateFormatter } from '@/Composables/dateFormatter';
import { Link } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

defineProps({
    incomes: Object,
});

const scout = reactive(useScoutHttpGet({ url: route('incomes.index') }));

const { df } = useDateFormatter();

const showFilters = ref(false);

const showCreateModal = ref(false);
</script>

<template>
    <div class="mb-10 sm:flex sm:items-start">
        <div class="sm:flex-auto">
            <h1 class="text-xl leading-6 font-semibold text-gray-900">Incomes</h1>
            <p class="mt-2 text-sm text-gray-700">A table of all recorded income.</p>
        </div>
        <div class="mt-3 sm:mt-0 sm:flex-none">
            <PrimaryButton @click="showCreateModal = true">Add income</PrimaryButton>
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
        <thead class="hidden md:table-header-group">
            <tr>
                <th scope="col" class="relative w-14 p-4">
                    <span class="sr-only">Toggle Information</span>
                </th>
                <TableHeader title="Source" field="source" :scout="scout" />
                <TableHeader title="Amount" field="amount" width="md:w-36" :scout="scout" />
                <TableHeader title="Date" field="effective_date" width="md:w-36" :scout="scout" />
                <th scope="col" class="relative p-4">
                    <span class="sr-only">Edit</span>
                </th>
            </tr>
        </thead>

        <tbody v-if="incomes.data.length === 0" class="divide-y divide-gray-200">
            <tr>
                <td></td>
                <td colspan="4" class="text-md py-3 text-gray-500">No income data found.</td>
            </tr>
        </tbody>

        <tbody v-else class="divide-y divide-y-reverse divide-gray-200">
            <template v-for="income in incomes.data" :key="income.id">
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
                                {{ income.source }}
                                <div class="flex items-center gap-2">
                                    <InformationCircleIcon v-if="income.notes" class="h-3 w-3 text-blue-400" />
                                    <!-- <TagIcon v-if="expense.has_receipt" class="h-3 w-3 text-gray-400" /> -->
                                    <CurrencyDollarIcon v-if="income.is_earned_income" class="h-3 w-3 text-green-700" />
                                </div>
                            </div>
                        </td>
                        <td class="w-[100px] py-3 align-baseline text-sm text-gray-500 lg:align-middle">
                            <span class="text-md font-bold lg:text-sm lg:font-normal">
                                {{ income.amount_pretty }}
                            </span>
                            <div class="md:hidden">
                                {{ income.effective_date_pretty }}
                            </div>
                        </td>
                        <td class="hidden py-3 text-sm text-gray-500 md:table-cell">
                            {{ income.effective_date_pretty }}
                        </td>
                        <td
                            class="hidden py-3 text-sm font-medium sm:table-cell sm:align-baseline md:w-14 lg:align-middle">
                            <Link
                                :href="route('incomes.edit', income.id)"
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
                                            <dt class="text-xs text-gray-800">Payment Date</dt>
                                            <dd class="text-sm leading-4">
                                                {{ df(income.payment_date) }}
                                            </dd>
                                        </div>
                                        <div class="grid grid-cols-table-dl gap-4 pb-1">
                                            <dt class="text-xs text-gray-800">Effective Date</dt>
                                            <dd class="text-sm leading-4">
                                                {{ df(income.effective_date) }}
                                            </dd>
                                        </div>
                                    </div>
                                    <div class="pb-3 sm:grid sm:grid-cols-table-dl sm:gap-4" v-show="income.notes">
                                        <dt class="pb-1 text-xs text-gray-800">Notes</dt>
                                        <dd
                                            class="markdown-field text-sm leading-4 text-gray-800"
                                            v-html="income.notes_html"></dd>
                                    </div>
                                </DisclosurePanel>
                            </transition>
                        </td>
                        <td colspan="3" class="align-baseline">
                            <DisclosurePanel class="leading-4 sm:hidden">
                                <Link
                                    :href="route('incomes.edit', income.id)"
                                    class="text-sm leading-4 text-indigo-600 underline hover:text-indigo-900">
                                    Edit
                                </Link>
                            </DisclosurePanel>
                        </td>
                    </tr>
                </Disclosure>
            </template>
        </tbody>
    </table>

    <Pagination :paginator="incomes" />

    <AddIncomeModal v-model="showCreateModal" />

    <FilterDialog :open="showFilters" :scout="scout" @close="showFilters = false">
        <DatePicker :scout="scout" />
    </FilterDialog>
</template>

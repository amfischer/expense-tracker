<script setup>
import ButtonLink from '@/Components/Buttons/ButtonLink.vue';
import { Link } from '@inertiajs/vue3';
import Pagination from './Pagination.vue';
import SearchBar from './SearchBar.vue';

defineProps({
    expenses: Object,
    categories: Array,
});
</script>

<template>
    <div class="sm:flex sm:items-start mb-10">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold leading-6 text-gray-900">Expenses</h1>
            <p class="mt-2 text-sm text-gray-700">A searchable and filterable table of all recorded expenses.</p>
        </div>
        <div class="sm:flex-none">
            <ButtonLink :href="route('expenses.create')">Add expense</ButtonLink>
        </div>
    </div>

    <SearchBar :expenses="expenses" :categories="categories" />

    <table class="min-w-full divide-y divide-gray-300 bg-white">
        <thead>
            <tr>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Payee</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Category</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Amount</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Date</th>
                <!-- <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Notes</th> -->
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Edit</span>
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            <tr v-for="expense in expenses.data" :key="expense.id">
                <td class="whitespace-nowrap py-3 text-sm text-gray-500">{{ expense.payee }}</td>
                <td class="whitespace-nowrap py-3 text-sm text-gray-500">{{ expense.category.name }}</td>
                <td class="whitespace-nowrap py-3 text-sm text-gray-500">
                    <span
                        v-if="expense.has_fees"
                        :title="'Foreign Currency Conversion Fee: ' + expense.foreign_currency_conversion_fee_pretty"
                        class="underline underline-offset-2 decoration-dotted cursor-pointer">
                        {{ expense.amount_pretty }}
                    </span>

                    <span v-else>{{ expense.amount_pretty }}</span>
                </td>
                <td class="whitespace-nowrap py-3 text-sm text-gray-500 dark:text-gray-300">
                    {{ expense.effective_date }}
                </td>
                <!-- <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.notes }}</td> -->

                <td class="whitespace-nowrap py-3 text-sm text-right font-medium">
                    <Link
                        :href="route('expenses.edit', expense.id)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-gray-200">
                        Edit
                    </Link>
                </td>
            </tr>
        </tbody>
    </table>

    <Pagination :expenses="expenses" />
</template>

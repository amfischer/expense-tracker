<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    expenses: Object,
});

</script>

<template>
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-t-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-6">Payee</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Category</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Amount</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Date</th>
                    <!-- <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Notes</th> -->
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="expense in expenses.data" :key="expense.id">
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300" sm:pl-6>{{ expense.payee }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.category.name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                        <span
                            v-if="expense.has_fees"
                            :title="'Foreign Currency Conversion Fee: ' + expense.foreign_currency_conversion_fee_pretty"
                            class="underline underline-offset-2 decoration-dotted cursor-pointer">
                            {{ expense.amount_pretty }}
                        </span>

                        <span v-else>{{ expense.amount_pretty }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.effective_date }}</td>
                    <!-- <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.notes }}</td> -->

                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <Link :href="route('expenses.edit', expense.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-gray-200">
                            Edit
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</template>

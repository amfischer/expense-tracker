<script setup>
import ButtonLink from '@/Components/Buttons/ButtonLink.vue';
import TableFilters from './TableFilters.vue';
import Pagination from './Pagination.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    expenses: Object,
});

const goToExpense = (expenseId) => {
    // only for mobile view, i.e. views without the edit button
    if (window.innerWidth >= 768) {
        return;
    }

    router.get(route('expenses.edit', expenseId))
}
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

    <TableFilters />

    <table class="min-w-full divide-y divide-gray-300 bg-white">
        <thead class="hidden md:table-header-group">
            <tr>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Payee</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Amount</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Date</th>
                <th scope="col" class="relative p-4">
                    <span class="sr-only">Edit</span>
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            <tr
                v-for="expense in expenses.data"
                :key="expense.id"
                class="hover:bg-gray-100 md:hover:bg-white"
                @click="goToExpense(expense.id)">
                <td class="whitespace-nowrap py-3 text-md text-gray-500">
                    {{ expense.payee }}
                    <div class="flex items-center gap-1 text-sm">
                        <span
                            class="rounded-full block w-2 h-2"
                            :style="{ backgroundColor: expense.category.color }"></span>
                        {{ expense.category.name }}
                    </div>
                </td>
                <td class="whitespace-nowrap py-3 text-sm text-gray-500">
                    <span
                        :title="
                            expense.has_fees
                                ? 'Foreign Currency Conversion Fee: ' + expense.foreign_currency_conversion_fee_pretty
                                : ''
                        "
                        :class="{ 'underline underline-offset-2 decoration-dotted cursor-pointer': expense.has_fees }"
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
                <td class="hidden md:table-cell whitespace-nowrap py-3 text-sm text-right font-medium">
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
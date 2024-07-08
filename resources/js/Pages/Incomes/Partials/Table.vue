<script setup>
import ButtonLink from '@/Components/Buttons/ButtonLink.vue';
import Pagination from '@/Components/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

defineProps({
    incomes: Object,
});

const goToIncome = (incomeId) => {
    // only for mobile view, i.e. views without the edit button
    if (window.innerWidth >= 768) {
        return;
    }

    router.get(route('income.edit', incomeId));
};
</script>

<template>
    <div class="sm:flex sm:items-start mb-10">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold leading-6 text-gray-900">Incomes</h1>
            <p class="mt-2 text-sm text-gray-700">A table of all recorded income.</p>
        </div>
        <div class="mt-3 sm:mt-0 sm:flex-none">
            <ButtonLink :href="route('incomes.create')">Add income</ButtonLink>
        </div>
    </div>

    <table class="min-w-full divide-y divide-gray-300 bg-white">
        <thead class="hidden md:table-header-group">
            <tr>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Source</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Amount</th>
                <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Date</th>
                <th scope="col" class="relative p-4">
                    <span class="sr-only">Edit</span>
                </th>
            </tr>
        </thead>

        <tbody v-if="incomes.data.length === 0" class="divide-y divide-gray-200">
            <tr>
                <td class="py-3 text-md text-gray-500">No income data found.</td>
            </tr>
        </tbody>

        <tbody v-else class="divide-y divide-gray-200">
            <tr
                v-for="income in incomes.data"
                :key="income.id"
                class="hover:bg-gray-100 md:hover:bg-white"
                @click="goToIncome(income.id)">
                <td class="whitespace-nowrap py-3 text-md text-gray-500">
                    <div class="flex items-center gap-2">
                        <span
                            :class="{ 'underline decoration-dotted underline-offset-1': income.notes }"
                            :title="income.notes">
                            {{ income.source }}
                        </span>
                    </div>
                </td>
                <td class="whitespace-nowrap py-3 text-sm text-gray-500">
                    <span class="font-bold text-md md:text-sm md:font-normal">
                        {{ income.amount_pretty }}
                    </span>

                    <div class="md:hidden">
                        {{ income.effective_date_pretty }}
                    </div>
                </td>
                <td class="hidden md:table-cell whitespace-nowrap py-3 text-sm text-gray-500">
                    {{ income.effective_date_pretty }}
                </td>
                <td class="hidden md:table-cell whitespace-nowrap py-3 text-sm text-right font-medium">
                    <Link
                        :href="route('incomes.edit', income.id)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-gray-200">
                        Edit
                    </Link>
                </td>
            </tr>
        </tbody>
    </table>

    <Pagination :paginator="incomes" />
</template>

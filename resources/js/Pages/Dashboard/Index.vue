<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Container from '@/Components/Container.vue';
import WhiteCard from '@/Components/WhiteCard.vue';
import BarChart from './Partials/BarChart.vue';
import CategoryMenu from './Partials/CategoryMenu.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CurrencyDollarIcon, TagIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    reports: Object,
});

const selectedReportIndex = ref(null);
const selectedCategory = ref(null);

const toggleReport = (index) => {
    if (selectedReportIndex.value === index) {
        selectedReportIndex.value = null;
        return;
    }

    selectedReportIndex.value = index;
    selectedCategory.value = props.reports[index].categories[0];
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <Container class="py-12">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Expense Totals</h3>
            <dl class="mt-5 grid grid-cols-2 gap-2 lg:gap-5 lg:grid-cols-4">
                <div
                    v-for="(report, index) in reports"
                    :key="index"
                    :class="{ 'border-2 border-red-400': selectedReportIndex === index }"
                    class="overflow-hidden rounded-lg bg-white shadow cursor-pointer px-4 py-5 sm:p-6"
                    @click="toggleReport(index)">
                    <div class="flex items-center justify-between">
                        <dt class="truncate text-sm font-medium text-gray-500">{{ report.label }}</dt>
                        <div
                            class="bg-blue-200 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-medium">
                            {{ report.count }}
                        </div>
                    </div>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ report.total }}</dd>
                </div>
            </dl>
        </Container>

        <Container class="py-12 hidden md:block" v-if="selectedReportIndex !== null">
            <WhiteCard>
                <BarChart :data="reports[selectedReportIndex] ?? {}" />
            </WhiteCard>
        </Container>

        <Container class="py-12" v-if="selectedReportIndex !== null">
            <WhiteCard class="flex-1">
                <div class="flex items-baseline justify-between mb-10">
                    <div>
                        <h3 class="text-xl font-semibold leading-6 text-gray-900">Category Details</h3>
                        <p class="mt-2 text-sm text-gray-700">Select a category to see underlying expenses.</p>
                    </div>

                    <CategoryMenu :categories="reports[selectedReportIndex].categories" v-model="selectedCategory" />
                </div>

                <table class="min-w-full divide-y divide-gray-300 bg-white">
                    <thead class="hidden md:table-header-group">
                        <tr>
                            <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900">Payee</th>
                            <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">
                                Amount
                            </th>
                            <th scope="col" class="py-4 text-left text-sm font-semibold text-gray-900 lg:w-40">Date</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="expense in selectedCategory.expenses" :key="expense.id" class="hover:bg-gray-100">
                            <td class="whitespace-nowrap py-3 text-md text-gray-500">
                                <div class="flex items-center gap-2">
                                    <span
                                        :class="{ 'underline decoration-dotted underline-offset-2': expense.notes }"
                                        :title="expense.notes">
                                        {{ expense.payee }}
                                    </span>
                                    <TagIcon v-if="expense.has_receipt" class="h-3 w-3 text-gray-500" />
                                    <CurrencyDollarIcon
                                        v-if="expense.is_business_expense"
                                        class="h-3 w-3 text-green-700" />
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
                                        'underline underline-offset-2 decoration-dotted cursor-pointer':
                                            expense.has_fees,
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
                        </tr>
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

        <Container class="py-48"></Container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Container from '@/Components/Container.vue';
import Report from './Partials/Report.vue';
import { ref } from 'vue';
import axios from 'axios';
import BarChart from './Partials/BarChart.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    reports: Object,
    paymentMethods: Array,
});

const selectedReportIndex = ref(null);
const selectedReportLabel = ref('');
const selectedReportCategories = ref([]);

const toggleReport = (index) => {
    // toggle off if already selected
    if (selectedReportIndex.value === index) {
        selectedReportIndex.value = null;
        selectedReportLabel.value = '';
        selectedReportCategories.value = [];
        return;
    }

    // toggle on and fetch data
    const config = {
        params: {
            date_from: props.reports[index].date_from,
            date_to: props.reports[index].date_to,
        },
    };

    axios
        .get(route('dashboard.summary.details'), config)
        .then((resp) => {
            selectedReportIndex.value = index;
            selectedReportLabel.value = props.reports[index].label;
            selectedReportCategories.value = resp.data.categories;
            console.log('success', resp);
        })
        .catch((err) => {
            console.error(err);
        });
};
</script>

<template>
    <Head title="Dashboard" />

    <template slot="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
    </template>

    <Container class="py-12">
        <h3 class="text-base font-semibold leading-6 text-gray-900">Expense Totals</h3>
        <dl class="mt-5 grid grid-cols-2 gap-2 lg:gap-5 lg:grid-cols-4">
            <div
                v-for="(report, index) in reports"
                :key="index"
                :class="{ 'border-2 border-red-400': selectedReportIndex === index }"
                class="overflow-hidden rounded-lg bg-white shadow cursor-pointer px-4 py-5 sm:px-6 sm:py-3"
                @click="toggleReport(index)">
                <div class="flex items-center justify-between">
                    <dt class="truncate text-sm font-medium text-gray-500">{{ report.label }}</dt>
                    <div
                        class="bg-blue-200 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-medium">
                        {{ report.count }}
                    </div>
                </div>
                <dd class="mt-1 tracking-tight text-gray-900">
                    <div class="flex items-center justify-between">
                        <span class="text-md">Expenses</span>
                        <span class="font-semibold">{{ report.total_expenses }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-md">Incomes</span>
                        <span class="font-semibold">{{ report.total_income }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-md"></span>
                        <span
                            class="font-semibold text-red-500"
                            :class="report.is_loss ? 'text-red-600' : 'text-emerald-600'">
                            {{ report.total_difference }}
                        </span>
                    </div>
                </dd>
            </div>
        </dl>
    </Container>

    <!-- <BarChart :label="selectedReportLabel" :categories="selectedReportCategories" /> -->

    <Report v-if="selectedReportIndex !== null" v-model="selectedReportCategories" :payment-methods="paymentMethods" />

    <Container class="py-48"></Container>
</template>

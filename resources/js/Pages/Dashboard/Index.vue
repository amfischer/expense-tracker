<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Container from '@/Components/Container.vue';
import TotalsSummaryCard from './Partials/TotalsSummaryCard.vue';
import Report from './Partials/Report.vue';
import BarChart from './Partials/BarChart.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    reports: Object,
    totals: Object,
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
        <h2 class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">Dashboard</h2>
    </template>

    <Container class="mt-12 mb-5">
        <h3 class="text-base leading-6 font-semibold text-gray-900">Expense Totals</h3>
    </Container>

    <Container class="mb-12" v-for="(year, index) in totals" :key="index">
        <dl class="grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
            <TotalsSummaryCard :report="year.summary" />
            <div></div>
            <div></div>
            <div></div>
            <TotalsSummaryCard v-for="(month, i) in year.months" :key="i" :report="month" />
        </dl>
    </Container>

    <!-- <BarChart :label="selectedReportLabel" :categories="selectedReportCategories" /> -->

    <Report v-if="selectedReportIndex !== null" v-model="selectedReportCategories" :payment-methods="paymentMethods" />

    <Container class="py-48"></Container>
</template>

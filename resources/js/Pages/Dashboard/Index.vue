<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Container from '@/Components/Container.vue';
import TotalsSummaryCard from './Partials/TotalsSummaryCard.vue';
import SummaryDialog from './Partials/SummaryDialog.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

defineOptions({ layout: AuthenticatedLayout });

defineProps({
    totals: Object,
});

// Summary Dialog state
const summaryDialogOpen = ref(false);
const summaryDialogData = ref(null);
const summaryDialogLoading = ref(false);
const summaryDialogLabel = ref('');

const openSummaryDialog = (report) => {
    summaryDialogOpen.value = true;
    summaryDialogLoading.value = true;
    summaryDialogLabel.value = report.label;
    summaryDialogData.value = null;

    axios
        .get(route('dashboard.summary.details'), {
            params: { date_from: report.date_from, date_to: report.date_to },
        })
        .then((resp) => {
            summaryDialogData.value = resp.data;
            summaryDialogLoading.value = false;
        })
        .catch((err) => {
            console.error(err);
            summaryDialogLoading.value = false;
        });
};

const closeSummaryDialog = () => {
    summaryDialogOpen.value = false;
};
</script>

<template>
    <Head title="Dashboard" />

    <Container class="mt-12 mb-5">
        <h3 class="text-base leading-6 font-semibold text-gray-900">Expense Totals</h3>
    </Container>

    <Container class="mb-12" v-for="(year, index) in totals" :key="index">
        <dl class="grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
            <TotalsSummaryCard :report="year.summary" @view="openSummaryDialog" />
            <div></div>
            <div></div>
            <div></div>
            <TotalsSummaryCard v-for="(month, i) in year.months" :key="i" :report="month" @view="openSummaryDialog" />
        </dl>
    </Container>

    <Container class="py-48"></Container>

    <SummaryDialog
        :open="summaryDialogOpen"
        :label="summaryDialogLabel"
        :data="summaryDialogData"
        :loading="summaryDialogLoading"
        @close="closeSummaryDialog" />
</template>

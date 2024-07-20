<script setup>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { computed, ref } from 'vue';

const props = defineProps({
    data: Object,
});

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const chartOptions = ref({ responsive: true });

const chartData = computed(() => ({
    labels: props.data.categories?.map((c) => c.name),
    datasets: [
        {
            label: props.data.label,
            data: props.data.categories?.map((c) => c.total_raw),
            backgroundColor: props.data.categories?.map((c) => c.color),
        },
    ],
}));
</script>

<template>
    <Bar id="my-chart-id" :options="chartOptions" :data="chartData" />
</template>

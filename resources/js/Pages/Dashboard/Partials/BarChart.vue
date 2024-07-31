<script setup>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';

const props = defineProps({
    label: String,
    categories: Array,
});

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const chartOptions = computed(() => ({
    responsive: true,
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context) {
                    const label = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(
                        context.parsed.y,
                    );
                    return label;
                },
            },
        },
    },
}));

const chartData = computed(() => ({
    labels: props.categories.map((c) => c.name),
    datasets: [
        {
            label: props.label,
            data: props.categories.map((c) => c.total_raw),
            backgroundColor: props.categories.map((c) => c.color),
        },
    ],
}));
</script>

<template>
    <Bar id="my-chart-id" :options="chartOptions" :data="chartData" />
</template>

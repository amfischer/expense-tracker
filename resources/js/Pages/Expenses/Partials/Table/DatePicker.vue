<script setup>
import { endOfMonth, endOfYear, startOfMonth, startOfYear, subMonths, startOfWeek, endOfWeek } from 'date-fns';
import '@vuepic/vue-datepicker/dist/main.css';
import VueDatePicker from '@vuepic/vue-datepicker';
import { useScoutStore } from '@/Stores/scout';
import { useWindowSize } from '@vueuse/core';
import { onMounted, ref } from 'vue';

onMounted(() => {
    let params = new URLSearchParams(document.location.search);

    let date_range = [];

    // the paginate strings provided by Laravel encode array values
    // so we need to manually set array values
    for (const [key, value] of params) {
        if (key.startsWith('date')) {
            date_range.push(value);
        }
    }

    scout.form.date = date_range;
});

const scout = useScoutStore();

const presetDates = ref([
    {
        label: 'Today',
        value: [new Date(), new Date()],
    },
    {
        label: 'This week',
        value: [startOfWeek(new Date()), endOfWeek(new Date())],
    },
    {
        label: 'This month',
        value: [startOfMonth(new Date()), endOfMonth(new Date())],
    },
    {
        label: 'Last month',
        value: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))],
    },
    {
        label: 'This year',
        value: [startOfYear(new Date()), endOfYear(new Date())],
    },
]);

const { width } = useWindowSize();
</script>

<template>
    <VueDatePicker
        v-model="scout.form.date"
        model-type="yyyy-MM-dd"
        :enable-time-picker="false"
        range
        :preset-dates="presetDates"
        :teleport-center="width > 600"
        @update:model-value="scout.search" />
</template>

<script setup>
import { endOfMonth, endOfYear, startOfMonth, startOfYear, subMonths, startOfWeek, endOfWeek } from 'date-fns';
import '@vuepic/vue-datepicker/dist/main.css';
import VueDatePicker from '@vuepic/vue-datepicker';
import { useWindowSize } from '@vueuse/core';
import { ref } from 'vue';

defineProps({
    scout: Object,
});

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
    <div class="border-t border-gray-200 px-4 py-6">
        <VueDatePicker
            v-model="scout.form.date"
            model-type="yyyy-MM-dd"
            :enable-time-picker="false"
            range
            :preset-dates="presetDates"
            :teleport-center="width > 600"
            placeholder="Select Date Range"
            @update:model-value="scout.search" />
    </div>
</template>

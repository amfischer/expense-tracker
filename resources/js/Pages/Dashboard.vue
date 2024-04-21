<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    year: Object,
    month: Object,
    lastMonth: Object,
    twoMonthsAgo: Object,
});

const stats = [
    { name: props.year.label, total: props.year.total, count: props.year.count },
    { name: props.month.label, total: props.month.total, count: props.month.count },
    { name: props.lastMonth.label, total: props.lastMonth.total, count: props.lastMonth.count },
    { name: props.twoMonthsAgo.label, total: props.twoMonthsAgo.total, count: props.twoMonthsAgo.count },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="w-full sm:max-w-2xl lg:max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Expense Totals</h3>
            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div
                    v-for="item in stats"
                    :key="item.name"
                    class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <div class="flex items-center justify-between">
                        <dt class="truncate text-sm font-medium text-gray-500">{{ item.name }}</dt>
                        <div
                            class="bg-blue-200 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-medium">
                            {{ item.count }}
                        </div>
                    </div>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ item.total }}</dd>
                </div>
            </dl>
        </div>
    </AuthenticatedLayout>
</template>

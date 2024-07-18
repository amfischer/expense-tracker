<script setup>
import Container from '@/Components/Container.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reports: Object,
});

const selectedReportIndex = ref(null);

const toggleReport = (index) => {
    if (selectedReportIndex.value === index) {
        selectedReportIndex.value = null;
        return;
    }

    selectedReportIndex.value = index;
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
            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
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

        <Container class="py-12" v-if="selectedReportIndex !== null">
            <ul role="list" class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                <li class="col-span-1" v-for="category in reports[selectedReportIndex].categories">
                    <div class="bg-white rounded-lg shadow p-3">
                        <h4 class="flex items-center gap-2 font-semibold mb-3">
                            <span class="rounded-full block w-2 h-2" :style="{ backgroundColor: category.color }">
                            </span>
                            {{ category.name }}
                        </h4>
                        <table class="min-w-full">
                            <tbody class="divide-y">
                                <tr v-for="expense in category.expenses">
                                    <td colspan="4" class="py-2">
                                        <span class="block text-sm">{{ expense.payee }}</span>
                                        <span class="text-xs">{{ expense.effective_date }}</span>
                                    </td>
                                    <td colspan="1" class="align-top text-right text-sm py-2">{{ expense.total }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="font-bold py-2">Total</td>
                                    <td colspan="1" class="font-bold text-right py-2">{{ category.total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </li>
            </ul>
        </Container>
    </AuthenticatedLayout>
</template>

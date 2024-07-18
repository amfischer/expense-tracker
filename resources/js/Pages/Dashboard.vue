<script setup>
import Container from '@/Components/Container.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
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
                    <Disclosure as="div" class="bg-white rounded-lg shadow pt-3 pb-1" v-slot="{ open }">
                        <DisclosureButton
                            class="w-full flex items-center gap-2 font-semibold px-3 pb-1 mb-2 border-b"
                            :class="open && 'open'">
                            <span class="rounded-full block w-4 h-4" :style="{ backgroundColor: category.color }">
                            </span>
                            <span class="flex-1 text-left">
                                {{ category.name }} <sup class="text-xs">({{ category.expenses.length }})</sup>
                            </span>
                            <span class="dropdown-arrow"></span>
                        </DisclosureButton>
                        <transition
                            enter-active-class="transition-all duration-300 ease-in overflow-hidden"
                            enter-from-class="transform max-h-0"
                            enter-to-class="transform max-h-96"
                            leave-active-class="transition-all duration-300 ease-out overflow-hidden"
                            leave-from-class="transform max-h-96"
                            leave-to-class="transform max-h-0">
                            <DisclosurePanel class="px-3">
                                <table class="min-w-full">
                                    <tbody class="divide-y">
                                        <tr v-for="expense in category.expenses">
                                            <td colspan="4" class="py-2">
                                                <span class="block text-sm">{{ expense.payee }}</span>
                                                <span class="text-xs">{{ expense.effective_date }}</span>
                                            </td>
                                            <td colspan="1" class="align-top text-right text-sm py-2">
                                                {{ expense.total }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </DisclosurePanel>
                        </transition>
                        <div class="flex items-center justify-between px-3">
                            <span class="font-bold pt-2">Total</span>
                            <span class="font-bold pt-2">{{ category.total }}</span>
                        </div>
                    </Disclosure>
                </li>
            </ul>
        </Container>
    </AuthenticatedLayout>
</template>

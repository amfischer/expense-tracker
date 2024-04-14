<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ExpensesTable from './Partials/Table/Table.vue';
import { Head } from '@inertiajs/vue3';
import { provide } from 'vue';

const props = defineProps({
    expenses: Object,
    categories: Array,
});

provide('categories', props.categories);
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <div class="w-full sm:max-w-2xl lg:max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
            <div class="rounded-lg shadow bg-white px-2 mx-2 py-10 sm:mx-0 sm:p-10">
                <ExpensesTable :expenses="expenses" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

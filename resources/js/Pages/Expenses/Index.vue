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
            <div class="rounded-lg p-10 bg-white">
                <ExpensesTable :expenses="expenses" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

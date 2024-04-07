<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ExpensesTable from './Partials/Table.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    expenses: Object,
    categories: Array,
});
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
            <div class="rounded-lg p-10 bg-white">
                <ExpensesTable :expenses="expenses" :categories="categories" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

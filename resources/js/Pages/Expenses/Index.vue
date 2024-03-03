<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ExpenseSearchBar from './Partials/SearchBar.vue';
import ExpensesTable from './Partials/Table.vue';
import ButtonLink from '@/Components/Buttons/ButtonLink.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    expenses: Object,
    categories: Array,
});

</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Expenses</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 sm:px-6 lg:px-8">

                    <div class="sm:flex sm:items-center sm:justify-end">
                        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            <ButtonLink :href="route('expenses.create')">Add expense</ButtonLink>
                        </div>
                    </div>

                    <div class="mt-8 flow-root">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <ExpenseSearchBar :categories="categories" />
                                <ExpensesTable :expenses="expenses" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

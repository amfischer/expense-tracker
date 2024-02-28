<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EditExpenseForm from './Partials/EditExpenseForm.vue';
import AlertSuccess from '@/Components/Alerts/Success.vue';
import ButtonLink from '@/Components/Buttons/ButtonLink.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    expense: Object,
    categories: Array,
    tags: Array,
    currencies: Array,
});

const successMessage = ref('');
const toggleAlert = (msg) => successMessage.value = msg;

</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Expense</h2>
        </template>

        <div class="relative">

            <AlertSuccess :message="successMessage" @close="successMessage = ''" class="max-w-5xl w-1/3 absolute top-7 left-0 right-0 mx-auto" />

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                    <div class="sm:flex sm:items-center sm:justify-end">
                        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            <ButtonLink :href="route('expenses.index')">Back</ButtonLink>
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <EditExpenseForm :expense="expense" :categories="categories" :tags="tags" :currencies="currencies" @expense-updated="toggleAlert" />
                    </div>

                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

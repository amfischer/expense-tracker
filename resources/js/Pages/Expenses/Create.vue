<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AddExpenseForm from './Partials/AddExpenseForm.vue';
import AlertSuccess from '@/Components/Alerts/Success.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
    categories: Array,
    tags: Array,
    currencies: Array,
});

const successMessage = ref('');
const toggleAlert = (msg) => (successMessage.value = msg);

const breadcrumbs = [{ name: 'Expenses', href: route('expenses.index'), current: false }];
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add Expense</h2>
            <Breadcrumbs class="pt-8 pb-0" :pages="breadcrumbs" />
        </template>

        <div class="relative">
            <AlertSuccess
                :message="successMessage"
                @close="successMessage = ''"
                class="max-w-5xl w-1/3 absolute top-7 left-0 right-0 mx-auto" />

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div class="rounded-lg shadow bg-white px-2 mx-2 py-10 sm:mx-0 sm:p-10">
                        <AddExpenseForm
                            :categories="categories"
                            :tags="tags"
                            :currencies="currencies"
                            @expense-created="toggleAlert" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

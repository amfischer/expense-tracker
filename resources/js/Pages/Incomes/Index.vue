<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IncomesTable from './Partials/Table.vue';
import { Head } from '@inertiajs/vue3';
import WhiteCard from '@/Components/WhiteCard.vue';

const props = defineProps({
    incomes: Object,
});
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <div class="w-full sm:max-w-2xl lg:max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
            <WhiteCard>
                <IncomesTable :incomes="incomes" />
            </WhiteCard>
        </div>
    </AuthenticatedLayout>
</template>

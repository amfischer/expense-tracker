<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IncomesTable from './Partials/Table.vue';
import { Head } from '@inertiajs/vue3';
import WhiteCard from '@/Components/WhiteCard.vue';
import Container from '@/Components/Container.vue';

const props = defineProps({
    incomes: Object,
});
</script>

<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <div class="py-12">
            <Container>
                <WhiteCard>
                    <IncomesTable :incomes="incomes" />
                </WhiteCard>
            </Container>
        </div>
    </AuthenticatedLayout>
</template>

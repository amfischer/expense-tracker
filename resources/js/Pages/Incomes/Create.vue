<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Container from '@/Components/Container.vue';
import WhiteCard from '@/Components/WhiteCard.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import AddIncomeForm from './Partials/AddIncomeForm.vue';

defineOptions({ layout: AuthenticatedLayout });

const breadcrumbs = [{ name: 'Incomes', href: route('incomes.index'), current: false }];
</script>

<template>
    <Head title="Incomes" />

    <template slot="header">
        <div class="flex items-center justify-between px-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add Income</h2>
            <Breadcrumbs :pages="breadcrumbs" />
        </div>
    </template>

    <div class="py-12">
        <Container>
            <WhiteCard>
                <AddIncomeForm />
            </WhiteCard>
        </Container>
    </div>
</template>

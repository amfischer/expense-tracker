<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Container from '@/Components/Container.vue';
import WhiteCard from '@/Components/WhiteCard.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import AddIncomeForm from './Partials/AddIncomeForm.vue';


const breadcrumbs = [{ name: 'Incomes', href: route('incomes.index'), current: false }];
</script>

<template>
    <Head title="Incomes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add Income</h2>
            <Breadcrumbs class="pt-8 pb-0" :pages="breadcrumbs" />
        </template>

        <div class="py-12">
            <Container>
                <WhiteCard>
                    <AddIncomeForm />
                </WhiteCard>
            </Container>
        </div>
    </AuthenticatedLayout>
</template>

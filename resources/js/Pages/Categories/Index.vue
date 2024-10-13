<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SimpleCard from './Partials/SimpleCard.vue';
import CreateModal from './Partials/CreateModal.vue';
import EditModal from './Partials/EditModal.vue';
import DeleteModal from './Partials/DeleteModal.vue';
import { Head } from '@inertiajs/vue3';
import { useCategoryStore } from '@/Stores/category.js';

defineOptions({ layout: AuthenticatedLayout });

defineProps({
    categories: Array,
});

const categoryStore = useCategoryStore();
</script>

<template>
    <Head title="Categories" />

    <template slot="header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6 sm:mb-0">Categories</h2>
            <PrimaryButton @click="categoryStore.openCreateModal()">Add category</PrimaryButton>
        </div>
    </template>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-6">
            <ul role="list" class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <SimpleCard v-for="category in categories" :key="category.id" :category="category" />
            </ul>
        </div>
    </div>

    <CreateModal />
    <EditModal />
    <DeleteModal />
</template>

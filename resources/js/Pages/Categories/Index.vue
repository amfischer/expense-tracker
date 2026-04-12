<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SimpleCard from './Partials/SimpleCard.vue';
import CreateModal from './Partials/CreateModal.vue';
import EditModal from './Partials/EditModal.vue';
import DeleteModal from './Partials/DeleteModal.vue';
import { Head } from '@inertiajs/vue3';
import { useCategoryStore } from '@/Stores/category.js';
import { computed } from 'vue';

const props = defineProps({
    categories: Array,
});

const parentCategories = computed(() => props.categories.map(({ id, name }) => ({ id, name })));

const categoryStore = useCategoryStore();
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="sm:flex sm:items-center sm:justify-between">
                <h2 class="mb-6 text-xl leading-tight font-semibold text-gray-800 sm:mb-0">Categories</h2>
                <PrimaryButton @click="categoryStore.openCreateModal()">Add category</PrimaryButton>
            </div>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 px-6 lg:px-8">
                <div v-for="category in categories" :key="category.id">
                    <ul role="list" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <SimpleCard :category="category" />
                        <SimpleCard
                            v-for="child in category.children"
                            :key="child.id"
                            :category="child"
                            :is-child="true" />
                    </ul>
                </div>
            </div>
        </div>
        <CreateModal :parent-categories="parentCategories" />
        <EditModal :parent-categories="parentCategories" />
        <DeleteModal />
    </AuthenticatedLayout>
</template>

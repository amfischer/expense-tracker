<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AlertSuccess from '@/Components/Alerts/Success.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SimpleCard from './Partials/SimpleCard.vue';
import CreateModal from './Partials/CreateModal.vue';
import EditModal from './Partials/EditModal.vue';
import DeleteModal from './Partials/DeleteModal.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    categories: Array,
});

const alertMessage = ref('');
const toggleAlert = (payload) => {
    closeModals();
    alertMessage.value = payload;
};

const selectedCategory = ref(null);

const showCreateModal = ref(false);

const showEditModal = ref(false);
const openEditModal = (payload) => {
    showEditModal.value = true;
    selectedCategory.value = payload;
};

const showDeleteModal = ref(false);
const openDeleteModal = (payload) => {
    showDeleteModal.value = true;
    selectedCategory.value = payload;
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
};
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Categories</h2>
        </template>

        <div class="relative">
            <AlertSuccess
                :message="alertMessage"
                @close="alertMessage = ''"
                class="max-w-5xl w-1/3 absolute top-7 left-0 right-0 mx-auto" />

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="sm:flex sm:items-center sm:justify-end">
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <PrimaryButton @click="showCreateModal = true">Add category</PrimaryButton>
                            </div>
                        </div>

                        <div class="mt-8 flow-root">
                            <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                    <ul
                                        role="list"
                                        class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
                                        <SimpleCard
                                            v-for="category in categories"
                                            :key="category.id"
                                            :category="category"
                                            @toggle-edit-modal="openEditModal"
                                            @toggle-delete-modal="openDeleteModal" />
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CreateModal :open="showCreateModal" @created="toggleAlert" @close="closeModals" />
        <EditModal :open="showEditModal" :category="selectedCategory" @updated="toggleAlert" @close="closeModals" />
        <DeleteModal :open="showDeleteModal" :category="selectedCategory" @deleted="toggleAlert" @close="closeModals" />
    </AuthenticatedLayout>
</template>

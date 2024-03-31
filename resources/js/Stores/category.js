import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useCategoryStore = defineStore('CategoryStore', () => {
    const selectedCategory = ref(null);

    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);

    const openCreateModal = () => (showCreateModal.value = true);

    const openEditModal = (payload) => {
        selectedCategory.value = payload;
        showEditModal.value = true;
    };

    const openDeleteModal = (payload) => {
        showDeleteModal.value = true;
        selectedCategory.value = payload;
    };

    const closeModals = () => {
        showCreateModal.value = false;
        showEditModal.value = false;
        showDeleteModal.value = false;
    };

    return {
        selectedCategory,
        showCreateModal,
        showEditModal,
        showDeleteModal,
        openCreateModal,
        openEditModal,
        openDeleteModal,
        closeModals,
    };
});

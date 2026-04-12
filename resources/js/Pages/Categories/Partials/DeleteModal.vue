<script setup>
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useCategoryStore } from '@/Stores/category.js';
import { useAlertStore } from '@/Stores/alert';

const categoryStore = useCategoryStore();
const alert = useAlertStore();

const errorMessage = ref('');

const closeModal = () => {
    categoryStore.closeModals();
    errorMessage.value = '';
};

const deleteCategory = () => {
    router.delete(route('categories.delete', categoryStore.selectedCategory.id), {
        preserveScroll: true,
        onSuccess: (resp) => {
            alert.setSuccessMessage(resp.props.flash.message, resp.props.flash.title);
            closeModal();
        },
        onError: (err) => {
            errorMessage.value = err.message;
            console.log('errors', err);
        },
    });
};
</script>

<template>
    <Modal :show="categoryStore.showDeleteModal" max-width="sm" @close="closeModal">
        <template #description>
            Are you sure you want to delete this category?

            <div class="my-4 text-center text-2xl font-bold">
                <span class="text-2xl font-bold">
                    {{ categoryStore.selectedCategory.name }}
                </span>
                <InputError :message="errorMessage" class="mt-2" />
            </div>

            <span class="text-sm italic">
                Note: to delete a category it must have no attached expenses or subcategories.
            </span>
        </template>

        <DangerButton @click="deleteCategory" class="float-end mt-6 table w-32">Delete</DangerButton>
    </Modal>
</template>

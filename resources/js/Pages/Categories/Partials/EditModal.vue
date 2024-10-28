<script setup>
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { useCategoryStore } from '@/Stores/category.js';
import { watch } from 'vue';
import { useAlertStore } from '@/Stores/alert';

const categoryStore = useCategoryStore();
const alert = useAlertStore();

const form = useForm({
    name: '',
    color: '#000000',
});

watch(
    () => categoryStore.selectedCategory,
    (newValue) => {
        (form.name = newValue.name), (form.color = newValue.color);
    },
);

const closeModal = () => {
    categoryStore.closeModals();
    form.clearErrors();
    // form.reset();
};

const updateCategory = () => {
    form.put(route('categories.update', categoryStore.selectedCategory.id), {
        preserveScroll: true,
        onSuccess: (resp) => {
            alert.setSuccessMessage(resp.props.flash.message, resp.props.flash.title);
            closeModal();
        },
        onError: () => {
            console.log('errors', form.errors);
        },
    });
};
</script>

<template>
    <Modal :show="categoryStore.showEditModal" max-width="sm" @close="closeModal">
        <!-- <template #header> Edit Category </template> -->

        <form @submit.prevent="updateCategory" class="space-y-4">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mb-5">
                <InputLabel for="color" value="Color" />
                <TextInput
                    id="color"
                    type="color"
                    class="mt-1 block w-14 h-14"
                    v-model="form.color"
                    required
                    autofocus />
                <InputError class="mt-2" :message="form.errors.color" />
            </div>

            <PrimaryButton type="submit" class="w-full justify-center rounded-md text-sm font-semibold">
                Update
            </PrimaryButton>
        </form>
    </Modal>
</template>

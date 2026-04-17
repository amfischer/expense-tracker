<script setup>
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { useAlertStore } from '@/Stores/alert';

const props = defineProps({
    show: Boolean,
    category: Object,
    parentCategories: Array,
});

const emit = defineEmits(['close']);
const alert = useAlertStore();

const form = useForm({
    name: '',
    color: '',
    parent_id: null,
});

// Exclude the current category from the parent options to prevent self-referencing
const availableParents = computed(() => props.parentCategories.filter((p) => p.id !== props.category?.id));

watch(
    () => props.category,
    (newValue) => {
        if (newValue) {
            form.name = newValue.name;
            form.color = newValue.color;
            form.parent_id = newValue.parent_id;
        }
    },
);

const applyParentColor = () => {
    const parent = props.parentCategories.find((p) => p.id === form.parent_id);

    if (parent) {
        form.color = parent.color;
    }
};

const closeModal = () => {
    emit('close');
    form.clearErrors();
};

const submit = () => {
    form.put(route('categories.update', props.category.id), {
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
    <Modal :show="show" max-width="sm" @close="closeModal">
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="color" value="Color" />
                <TextInput id="color" type="color" class="mt-1 block h-14 w-14" v-model="form.color" required />
                <InputError class="mt-2" :message="form.errors.color" />
            </div>

            <div>
                <InputLabel for="parent_id" value="Parent Category (optional)" />
                <select
                    id="parent_id"
                    v-model="form.parent_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                    <option :value="null">None</option>
                    <option v-for="parent in availableParents" :key="parent.id" :value="parent.id">
                        {{ parent.name }}
                    </option>
                </select>
                <button
                    type="button"
                    v-show="form.parent_id"
                    class="mt-2 rounded-md border bg-white px-4 py-2 text-gray-800 hover:bg-gray-400"
                    @click="applyParentColor">
                    Use parent color
                </button>
                <InputError class="mt-2" :message="form.errors.parent_id" />
            </div>

            <PrimaryButton type="submit" class="w-full justify-center rounded-md text-sm font-semibold">
                Update
            </PrimaryButton>
        </form>
    </Modal>
</template>

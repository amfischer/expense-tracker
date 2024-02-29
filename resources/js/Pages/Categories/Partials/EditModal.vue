<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    open: Boolean,
    category: Object
});

const form = useForm({
    name: '',
    color: '',
    abbreviation: '',
});

watch(() => props.category, (newValue, oldValue) => {
    form.name = newValue.name
    form.color = newValue.color
    form.abbreviation = newValue.abbreviation
});

watch(() => props.open, (newValue, oldValue) => {
    if (! newValue) {
        form.clearErrors();
    }
});

const emit = defineEmits(['close', 'updated']);

const update = () => {
    form.put(route('categories.update', props.category.id), {
        preserveScroll: true,
        onSuccess: (resp) => {
            emit('updated', resp.props.flash.message)
        },
        onError: () => {
            console.log('errors', form.errors)
        },
    });
}

</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-10" @close="$emit('close')">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6">

                            <DialogTitle as="h3" class="text-base text-center font-semibold leading-6 text-gray-900">
                                {{ 'Edit Category: ' + category.name }}
                            </DialogTitle>

                            <form @submit.prevent="create" class="space-y-4 py-4">
                                <div>
                                    <InputLabel for="name" value="Name" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel for="abbreviation" value="Abbreviation" />
                                    <TextInput
                                        id="abbreviation"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.abbreviation"
                                        required
                                        autofocus />
                                    <InputError class="mt-2" :message="form.errors.abbreviation" />
                                </div>

                                <div>
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
                            </form>
                            <div class="mt-6">
                                <button
                                    type="button"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    @click="update">
                                    Update
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { Dialog, DialogPanel, DialogTitle, DialogDescription, TransitionChild, TransitionRoot } from '@headlessui/vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    open: Boolean,
    category: Object
});

const errorMessage = ref('');

watch(() => props.open, (newValue, oldValue) => {
    if (! newValue) {
        errorMessage.value = ''
    }
});

const emit = defineEmits(['close', 'deleted']);

const deleteCategory = () => {
    router.delete(route('categories.delete', props.category.id), {
        preserveScroll: true,
        onSuccess: (resp) => {
            emit('deleted', resp.props.flash.message)
        },
        onError: (err) => {
            errorMessage.value = err.message
            console.log('errors', err)
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
                                {{ 'Delete Category: ' + category.name }}
                            </DialogTitle>

                            <DialogDescription as="p" class="my-6 text-xl">
                                This will permanently delete the category.
                                <InputError :message="errorMessage" class="mt-2"></InputError>
                            </DialogDescription>


                            <DangerButton @click="deleteCategory" class="table w-full">Delete</DangerButton>

                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

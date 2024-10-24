<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import FilterOption from './FilterOption.vue';
import DatePicker from './DatePicker.vue';
import { inject } from 'vue';

defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    scout: Object,
});

const categories = inject('categories');
const paymentMethods = inject('paymentMethods');

defineEmits(['close']);
</script>

<template>
    <!-- Filter dialog -->
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-40" @close="$emit('close')">
            <TransitionChild
                as="template"
                enter="transition-opacity ease-linear duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="transition-opacity ease-linear duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0">
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-0 z-40 flex">
                <TransitionChild
                    as="template"
                    enter="transition ease-in-out duration-300 transform"
                    enter-from="translate-x-full"
                    enter-to="translate-x-0"
                    leave="transition ease-in-out duration-300 transform"
                    leave-from="translate-x-0"
                    leave-to="translate-x-full">
                    <DialogPanel
                        class="relative ml-auto flex h-full w-full max-w-xs flex-col overflow-y-auto bg-white py-4 pb-6 shadow-xl">
                        <div class="relative flex items-center justify-between px-4 pb-4">
                            <h2 class="text-lg font-medium text-gray-900">Filters</h2>
                            <button
                                type="button"
                                class="-mr-2 flex h-10 w-10 items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                @click="$emit('close')">
                                <span class="sr-only">Close menu</span>
                                <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                            </button>
                            <button
                                type="button"
                                class="absolute bottom-1 rounded bg-indigo-600 px-2 text-xs text-white shadow-sm hover:bg-indigo-500"
                                @click="scout.clearFilters">
                                Clear
                            </button>
                        </div>

                        <!-- Filters -->
                        <form>
                            <FilterOption
                                title="Categories"
                                field="category_ids"
                                :options="categories"
                                :scout="scout" />
                            <FilterOption
                                title="Payment Methods"
                                field="payment_methods"
                                :options="paymentMethods"
                                :scout="scout" />
                            <!-- <DatePicker /> -->
                        </form>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

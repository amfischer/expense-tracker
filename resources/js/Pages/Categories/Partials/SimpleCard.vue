<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid';
import { useCategoryStore } from '@/Stores/category.js';

defineProps({
    category: Object,
    isChild: {
        type: Boolean,
        default: false,
    },
});

const categoryStore = useCategoryStore();
</script>

<template>
    <li class="col-span-1 flex rounded-md shadow-xs" :class="{ 'ml-6': isChild }">
        <div
            class="flex w-16 shrink-0 items-center justify-center rounded-l-md text-sm font-medium text-white"
            :style="{ 'background-color': category.color }"></div>
        <div
            class="flex flex-1 items-center justify-between rounded-r-md border-t border-r border-b border-gray-200 bg-white">
            <div class="flex-1 px-4 py-2 text-sm">
                <p class="font-medium text-gray-900 hover:text-gray-600">{{ category.name }}</p>
                <p class="text-gray-500">{{ category.expenses_count + ' expenses' }}</p>
            </div>
            <Menu as="div" class="relative inline-block shrink-0 pr-2 text-left">
                <div>
                    <MenuButton
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                        <span class="sr-only">Open options</span>
                        <EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95">
                    <MenuItems
                        class="absolute right-0 z-10 mt-2 w-28 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden">
                        <div class="py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    type="button"
                                    :class="[
                                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                        'w-full px-4 py-2 text-left text-sm',
                                    ]"
                                    @click="categoryStore.openEditModal(category)">
                                    Edit
                                </button>
                            </MenuItem>
                            <MenuItem v-slot="{ active }">
                                <button
                                    type="button"
                                    :class="[
                                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                        'w-full px-4 py-2 text-left text-sm',
                                    ]"
                                    @click="categoryStore.openDeleteModal(category)">
                                    Delete
                                </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </div>
    </li>
</template>

<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';


const props = defineProps({
    categories: Array
});

const modelValue = defineModel({
    type: Object,
});


const triggerSort = (item) => {
    modelValue.value = item;
    // scout.search();
};
</script>

<template>
    <Menu as="div" class="relative inline-block text-left">
        <MenuButton class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
            Category
            <ChevronDownIcon
                class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500"
                aria-hidden="true" />
        </MenuButton>
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <MenuItems
                class="absolute right-0 z-10 mt-2 w-32 origin-top-left rounded-md bg-white shadow-2xl focus:outline-none">
                <div class="py-1">
                    <MenuItem v-for="category in categories" :key="category.id" v-slot="{ active }">
                        <span
                            :class="[
                                active ? 'bg-gray-100' : '',
                                category === modelValue ? 'text-gray-900' : 'text-gray-500',
                                'block px-4 py-2 text-sm font-medium cursor-pointer',
                            ]"
                            @click="triggerSort(category)">
                            {{ category.name }}
                        </span>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

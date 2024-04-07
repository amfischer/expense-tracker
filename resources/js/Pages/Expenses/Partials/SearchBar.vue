<script setup>
import {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
} from '@headlessui/vue';
import { ChevronDownIcon, MagnifyingGlassIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    expenses: Object,
    categories: Array,
});

const searchForm = useForm({
    query: '',
    category_ids: [],
});

onMounted(() => {
    const url = new URL(window.location.href);

    searchForm.query = url.searchParams.get('query');
});

const sortOptions = [
    { name: 'Date', href: '#' },
    { name: 'Amount', href: '#' },
    { name: 'Category', href: '#' },
];

const searchAndFilter = useDebounceFn(() => {
    searchForm.get(route('expenses.index'), {
        preserveState: true,
        onSuccess: (resp) => {
            console.log('success', resp);
        },
    });
}, 400);
</script>

<template>
    <div class="flex items-center justify-between mb-10">
        <div class="relative basis-80">
            <TextInput class="px-3 py-1 w-full max-w-sm" v-model="searchForm.query" @keydown="searchAndFilter" />
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </div>
        </div>

        <div class="flex items-center gap-8">
            <PopoverGroup class="hidden sm:flex sm:items-baseline sm:space-x-8">
                <Popover as="div" id="desktop-menu-category" class="relative inline-block text-left">
                    <div>
                        <PopoverButton
                            class="group inline-flex items-center justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
                            <span>Category</span>
                            <span
                                v-if="searchForm.category_ids.length > 0"
                                class="ml-1.5 rounded bg-gray-200 px-1.5 py-0.5 text-xs font-semibold tabular-nums text-gray-700">
                                {{ searchForm.category_ids.length }}
                            </span>
                            <ChevronDownIcon
                                class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500"
                                aria-hidden="true" />
                        </PopoverButton>
                    </div>
                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95">
                        <PopoverPanel
                            class="absolute right-0 z-10 mt-2 origin-top-right rounded-md bg-white p-4 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <form class="space-y-4">
                                <div v-for="category in categories" :key="category.id" class="flex items-center">
                                    <input
                                        :id="`filter-${category.id}`"
                                        v-model="searchForm.category_ids"
                                        :value="category.id"
                                        @change="searchAndFilter"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label
                                        :for="`filter-${category.id}`"
                                        class="ml-3 whitespace-nowrap pr-6 text-sm font-medium text-gray-900">
                                        {{ category.name }}
                                    </label>
                                </div>
                            </form>
                        </PopoverPanel>
                    </transition>
                </Popover>
            </PopoverGroup>
            <Menu as="div" class="relative inline-block text-left">
                <MenuButton class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
                    Sort
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
                            <MenuItem v-for="option in sortOptions" :key="option" v-slot="{ active }">
                                <a
                                    :href="option.href"
                                    :class="[
                                        active ? 'bg-gray-100' : '',
                                        'block px-4 py-2 text-sm font-medium text-gray-900',
                                    ]">
                                    {{ option.name }}
                                </a>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </div>
    </div>
</template>

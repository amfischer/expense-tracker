<script setup>
import {
  Popover,
  PopoverButton,
  PopoverGroup,
  PopoverPanel,
} from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    categories: Array,
});

const filters = ref({
    category_ids: []
})

const searchAndFilter = () => {
    // deep copy of filters object
    const data = JSON.parse(JSON.stringify(filters.value))

    if (data.category_ids.length === 0) {
        delete data.category_ids
    }

    router.get(route('expenses.index'), data, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (resp) => {
            console.log('success', resp)
        },
    })
}
</script>

<template>
    <div class="shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg mb-3">
        <div class="bg-gray-50">
            <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:max-w-7xl lg:px-8">

                <section class="py-6">
                    <div class="flex items-center justify-end">
                        <PopoverGroup class="hidden sm:flex sm:items-baseline sm:space-x-8">
                            <Popover
                                as="div"
                                id="desktop-menu-category"
                                class="relative inline-block text-left">
                                <div>
                                    <PopoverButton class="group inline-flex items-center justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                        <span>Category</span>
                                        <span
                                            v-if="filters.category_ids.length > 0"
                                            class="ml-1.5 rounded bg-gray-200 px-1.5 py-0.5 text-xs font-semibold tabular-nums text-gray-700">
                                            {{ filters.category_ids.length }}
                                        </span>
                                        <ChevronDownIcon class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                                    </PopoverButton>
                                </div>

                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                                    <PopoverPanel class="absolute right-0 z-10 mt-2 origin-top-right rounded-md bg-white p-4 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <form class="space-y-4">
                                            <div v-for="category in categories" :key="category.id" class="flex items-center">
                                                <input
                                                    :id="`filter-${category.id}`"
                                                    v-model="filters.category_ids"
                                                    :value="category.id"
                                                    @change="searchAndFilter"
                                                    type="checkbox"
                                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <label :for="`filter-${category.id}`" class="ml-3 whitespace-nowrap pr-6 text-sm font-medium text-gray-900">{{ category.name }}</label>
                                            </div>
                                        </form>
                                    </PopoverPanel>
                                </transition>
                            </Popover>
                        </PopoverGroup>
                    </div>
                </section>

            </div>
        </div>
    </div>
</template>

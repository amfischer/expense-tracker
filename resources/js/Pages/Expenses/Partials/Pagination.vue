<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    expenses: Object,
});

const pageLinks = computed(() => {
    const links = props.expenses.links;
    links.pop();
    links.shift();
    return links;
})

</script>

<template>
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-b-lg">
        <div class="flex items-center justify-between bg-white p-4 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                <Link
                    :as="expenses.prev_page_url ? 'a' : 'span'"
                    :href="expenses.prev_page_url ?? ''"
                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Previous
                </Link>
                <Link
                    :as="expenses.next_page_url ? 'a' : 'span'"
                    :href="expenses.next_page_url ?? ''"
                    class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Next
                </Link>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">{{ expenses.from }}</span>
                        to
                        <span class="font-medium">{{ expenses.to }}</span>
                        of
                        <span class="font-medium">{{ expenses.total }}</span>
                        results
                    </p>
                </div>
                <div>
                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <Link
                            :as="expenses.prev_page_url ? 'a' : 'span'"
                            :href="expenses.prev_page_url ?? ''"
                            class="relative inline-flex items-center rounded-l-md px-2 py-2 ring-1 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                            :class="expenses.prev_page_url ? 'text-gray-500 ring-gray-300' : 'text-gray-300 ring-gray-200'">
                            <span class="sr-only">Previous</span>
                            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                        </Link>

                        <Link
                            v-for="(link, i) in pageLinks"
                            :key="i"
                            :href="link.url"
                            aria-current="page"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold"
                            :class="link.active ? 'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'">
                            {{ link.label }}
                        </Link>
                        <!-- <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span> -->

                        <Link
                            :as="expenses.next_page_url ? 'a' : 'span'"
                            :href="expenses.next_page_url ?? ''"
                            class="relative inline-flex items-center rounded-r-md px-2 py-2 ring-1 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                            :class="expenses.next_page_url ? 'text-gray-500 ring-gray-300' : 'text-gray-300 ring-gray-200'">
                            <span class="sr-only">Next</span>
                            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                        </Link>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

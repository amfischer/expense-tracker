<script setup>
import ButtonLink from '@/Components/Buttons/ButtonLink.vue';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import { Head, Link } from '@inertiajs/vue3';
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
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-t-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-6">Payee</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Category</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Tags</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Total</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Date</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Notes</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="expense in expenses.data" :key="expense.id">
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300" sm:pl-6>{{ expense.payee }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.category.name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.tags_pretty }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                        <span
                            v-if="expense.has_fees"
                            :title="'Amount: ' + expense.amount_pretty + ' &mdash; Fees: ' + expense.fees_pretty"
                            class="underline underline-offset-2 decoration-dotted cursor-pointer">
                            {{ expense.total }}
                        </span>

                        <span v-else>{{ expense.total }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.effective_date }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.notes }}</td>

                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <Link :href="route('expenses.edit', expense.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-gray-200">
                            Edit
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-b-lg">
        <div class="flex items-center justify-between bg-white p-4 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                <Link 
                    :as="expenses.prev_page_url ? 'a' : 'span'"
                    :href="expenses.prev_page_url"
                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Previous
                </Link>
                <Link
                    :as="expenses.next_page_url ? 'a' : 'span'"
                    :href="expenses.next_page_url" 
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
                            :href="expenses.prev_page_url"
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
                            :href="expenses.next_page_url"
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

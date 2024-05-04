<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    expenses: Object,
});

const hasPreviousLink = computed(() => props.expenses.prev_page_url !== null);
const hasNextLink = computed(() => props.expenses.next_page_url !== null);
</script>

<template>
    <nav class="flex items-center justify-between mt-10" aria-label="Pagination">
        <p class="hidden sm:block text-sm text-gray-700">
            Showing
            <span class="font-medium">{{ expenses.from }}</span>
            to
            <span class="font-medium">{{ expenses.to }}</span>
            of
            <span class="font-medium">{{ expenses.total }}</span>
            results
        </p>
        <div class="flex-1 sm:flex-none flex items-center justify-between gap-3">
            <Link
                :href="expenses.prev_page_url"
                as="button"
                :disabled="!hasPreviousLink"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium"
                :class="hasPreviousLink ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-400'">
                Previous
            </Link>
            <Link
                :href="expenses.next_page_url"
                as="button"
                :disabled="!hasNextLink"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium"
                :class="hasNextLink ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-400'">
                Next
            </Link>
        </div>
    </nav>
</template>

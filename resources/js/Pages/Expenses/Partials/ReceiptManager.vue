<script setup>
import { DocumentTextIcon, MinusCircleIcon } from '@heroicons/vue/24/solid';
import AddReceiptForm from './AddReceiptForm.vue';
import ShowReceiptModal from './ShowReceiptModal.vue';
import DeleteReceiptModal from './DeleteReceiptModal.vue';
import { ref } from 'vue';

const props = defineProps({
    expense: Object,
    receipt: Object,
});

const showViewModal = ref(false);
const showDeleteModal = ref(false);
</script>

<template>
    <header class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Manage Receipt(s)</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Currently, only one receipt per expense is allowed.</p>
    </header>

    <template v-if="receipt === null">
        <AddReceiptForm :expense="expense" />
    </template>

    <template v-else>
        <div class="flex items-center gap-4 rounded-lg border border-gray-300 px-3 py-2 md:w-1/2">
            <DocumentTextIcon class="h-7 w-7" />
            <div class="grow flex flex-col cursor-pointer hover:underline underline-offset-2" @click="showViewModal = true">
                <span class="text-md text-gray-900">{{ receipt.mimetype }}</span>
                <span class="text-sm text-gray-600">{{ receipt.size_formatted }}</span>
            </div>
            <MinusCircleIcon class="h-5 w-5 text-red-500 cursor-pointer" @click="showDeleteModal = true" />
        </div>

        <ShowReceiptModal v-model="showViewModal" :expense="expense" :receipt="receipt" />
        <DeleteReceiptModal v-model="showDeleteModal" :expense="expense" :receipt="receipt" />
    </template>
</template>

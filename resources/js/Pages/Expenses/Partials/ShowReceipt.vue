<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import { useReceiptStore } from '@/Stores/receipt';

const props = defineProps({
    receipt: Object,
});

const store = useReceiptStore();
</script>

<template>
    <div>
        <div
            class="flex items-center justify-center rounded-lg border border-dashed border-gray-400 p-10 md:w-1/2 md:p-5">
            <img :src="'data:image;base64,' + receipt.image_contents" alt="" />
        </div>
        <div class="flex items-center gap-4 w-full mt-10">
            <DangerButton @click="store.openDeleteModal(props.receipt)">Delete</DangerButton>
        </div>
    </div>
</template>

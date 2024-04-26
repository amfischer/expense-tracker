<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import { useReceiptStore } from '@/Stores/receipt';
import { ref } from 'vue';

const props = defineProps({
    receipt: Object,
});

const store = useReceiptStore();

const showImageModal = ref(false);
const showPdfModal = ref(false);

const toggleModal = () => {
    if (props.receipt.is_image) {
        showImageModal.value = true;
        return;
    }

    showPdfModal.value = true;
};
</script>

<template>
    <div>
        <div
            class="flex items-center justify-center rounded-lg border border-dashed border-gray-400 p-10 md:w-1/2 md:p-5 cursor-pointer relative">
            <div class="z-20 bg-black opacity-0 hover:opacity-10 h-full w-full absolute" @click="toggleModal"></div>

            <img v-if="receipt.is_image" :src="'data:image;base64,' + receipt.base64" class="max-h-[500px]" />
            <iframe v-else :src="'data:application/pdf;base64,' + receipt.base64" width="100%" height="100%" />
        </div>
        <div class="flex items-center gap-4 w-full mt-10">
            <DangerButton @click="store.openDeleteModal(props.receipt)">Delete</DangerButton>
        </div>
    </div>

    <Modal :show="showImageModal" @close="showImageModal = false">
        <img :src="'data:image;base64,' + receipt.base64" />
    </Modal>

    <Modal
        :show="showPdfModal"
        @close="showPdfModal = false"
        max-width="6xl"
        dialog-panel-classes="h-screen my-10">
        <iframe :src="'data:application/pdf;base64,' + receipt.base64" width="100%" height="100%" />
    </Modal>
</template>

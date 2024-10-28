<script setup>
import Modal from '@/Components/Modal.vue';
import { computed } from 'vue';

const props = defineProps({
    expense: Object,
    receipt: Object,
});

const show = defineModel({ type: Boolean, default: false });

const src = computed(() => {
    if (props.expense && props.receipt) {
        return route('expenses.receipts.show', { expense: props.expense.id, receipt: props.receipt.id });
    }

    // todo - add default not found image
    return '';
});

const maxWidth = computed(() => (props.receipt?.is_image ? '2xl' : '6xl'));

const panelClasses = computed(() => {
    if (props.receipt?.is_image) {
        return 'p-0';
    }

    return 'h-[calc(100vh-4rem)] p-0';
});
</script>

<template>
    <Modal :show="show" @close="show = false" :max-width="maxWidth" :dialog-panel-classes="panelClasses">
        <template v-if="receipt.is_image">
            <img :src="src" />
        </template>
        <template v-else>
            <iframe :src="src" width="100%" height="100%" />
        </template>
    </Modal>
</template>

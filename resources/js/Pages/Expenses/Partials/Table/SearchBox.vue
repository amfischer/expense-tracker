<script setup>
import TextInput from '@/Components/TextInput.vue';
import { XMarkIcon } from '@heroicons/vue/20/solid';

const query = defineModel({
    type: String,
});

defineEmits(['reset']);
</script>

<template>
    <div class="relative w-full basis-80">
        <TextInput class="px-3 py-1 w-full max-w-sm" v-model="query" />
        <div class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3" @click="$emit('reset')">
            <XMarkIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </div>
    </div>
</template>

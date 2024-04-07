<script setup>
import TextInput from '@/Components/TextInput.vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid';

const query = defineModel({
    type: String,
});
</script>

<template>
    <div class="relative basis-80">
        <TextInput class="px-3 py-1 w-full max-w-sm" v-model="query" />
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </div>
    </div>
</template>

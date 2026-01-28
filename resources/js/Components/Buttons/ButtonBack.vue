<script setup>
import { ArrowLeftIcon } from '@heroicons/vue/20/solid';

const back = () => window.history.back();
</script>
<template>
    <button
        class="inline-flex items-center gap-2 px-3 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        type="button"
        @click="back">
        <ArrowLeftIcon class="h-4 w-4 text-white" />
        Back
    </button>
</template>

<script setup>
import { CheckCircleIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import { useAlertStore } from '@/Stores/alert.js';

const alert = useAlertStore();
</script>

<template>
    <div class="rounded-lg bg-green-50 p-4" v-show="alert.message !== ''">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <CheckCircleIcon class="h-5 w-5 text-green-400" aria-hidden="true" />
            </div>
            <div class="ml-3">
                <p class="text-lg font-medium text-green-800">{{ alert.message }}</p>
            </div>
            <div class="ml-auto pl-3">
                <button
                    type="button"
                    @click="alert.clear()"
                    class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50">
                    <span class="sr-only">Dismiss</span>
                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                </button>
            </div>
        </div>
    </div>
</template>

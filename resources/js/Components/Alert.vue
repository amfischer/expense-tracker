<script setup>
import { CheckCircleIcon, ExclamationCircleIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import { useAlertStore } from '@/Stores/alert.js';
import { storeToRefs } from 'pinia';

const store = useAlertStore();

const { title, message, status, show } = storeToRefs(store);
</script>

<template>
    <div class="fixed w-max max-w-full z-50 mx-auto left-0 right-0 sm:mr-0 sm:right-10 bottom-10 shadow-xl">
        <Transition name="fade">
            <div
                v-if="show"
                class="font-medium w-[calc(100vw-40px)] max-w-[400px] rounded-md"
                :class="{
                    'bg-green-200': status === 'success',
                    'bg-red-200': status === 'error',
                }"
                role="alert"
                aria-live="assertive"
                aria-atomic="true">
                <div class="flex items-center rounded-t-md p-3">
                    <CheckCircleIcon
                        v-show="status === 'success'"
                        class="h-5 w-5 text-green-600 mr-3"
                        aria-hidden="true" />
                    <ExclamationCircleIcon
                        v-show="status === 'error'"
                        class="h-5 w-5 text-red-600 mr-3"
                        aria-hidden="true" />
                    <strong class="mr-auto">{{ title }}</strong>
                    <button type="button" @click="store.hide">
                        <span class="sr-only">Dismiss</span>
                        <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>
                <div class="bg-white text-wrap rounded-b-md p-3 text-sm">
                    {{ message }}
                </div>
            </div>
        </Transition>
    </div>
</template>

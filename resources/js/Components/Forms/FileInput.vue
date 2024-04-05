<script setup>
import { PhotoIcon } from '@heroicons/vue/20/solid';
import { computed } from 'vue';

const model = defineModel({
    type: [File, String],
    required: true,
});

const fileUploaded = computed(() => {
    return model.value instanceof File;
});
</script>

<template>
    <div
        class="flex flex-col items-center justify-center gap-5 rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
        <div class="text-center">
            <PhotoIcon class="mx-auto h-12 w-12 text-gray-300" aria-hidden="true" />
            <div class="mt-4 flex text-sm leading-6 text-gray-600">
                <label
                    for="file-upload"
                    class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 hover:text-indigo-500">
                    <span>Upload a file</span>
                    <input id="file-upload" type="file" class="sr-only" @input="model = $event.target.files[0]" />
                </label>
                <p class="pl-1">or drag and drop</p>
            </div>
            <p class="text-xs leading-5 text-gray-600">PNG, JPG, WEBP up to 1MB</p>
        </div>
        <p class="text-sm text-center" v-if="fileUploaded">{{ model.name }}</p>
    </div>
</template>

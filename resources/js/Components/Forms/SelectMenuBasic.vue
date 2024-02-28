<script setup>

const props = defineProps({
    options: Array
})

const model = defineModel({
    type: String,
    required: true,
});


</script>

<template>
    <select 
        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
        v-model="model">
        <option v-for="(option, i) in options" :key="i" :value="option">
            {{ option }}
        </option>
    </select>
</template>

<script setup>
defineProps({
    options: Object,
    hasKeys: {
        type: Boolean,
        default: false,
    },
});

const model = defineModel({
    type: [String, Number],
    required: true,
});
</script>

<template>
    <select
        class="mt-2 block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
        v-model="model">
        <template v-if="hasKeys">
            <option value="" class="text-gray-400">None</option>
            <option v-for="(value, key, index) in options" :key="index" :value="key">
                {{ value }}
            </option>
        </template>

        <template v-else>
            <option data-else="test" v-for="(option, i) in options" :key="i" :value="option">
                {{ option }}
            </option>
        </template>
    </select>
</template>

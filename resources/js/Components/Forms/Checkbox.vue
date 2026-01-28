<script setup>

const model = defineModel({
    type: Array,
    required: true,
});

</script>

<template>
    <input
        class="h-4 w-4 rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-600"
        v-model="model"
        type="checkbox"
    />
</template>

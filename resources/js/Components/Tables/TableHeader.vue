<script setup>
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid';
import { computed } from 'vue';

const props = defineProps({
    field: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    scout: {
        type: Object,
        required: true,
    },
    width: {
        type: String,
        required: false,
        default: '',
    },
});

const classes = computed(() => 'py-4 text-left text-sm font-semibold text-gray-900 cursor-pointer' + ' ' + props.width);

const showArrowDown = computed(() => props.scout.form.sort_by === props.field && props.scout.form.sort_dir === 'asc');
const showArrowUp = computed(() => props.scout.form.sort_by === props.field && props.scout.form.sort_dir === 'desc');
</script>

<template>
    <th scope="col" :class="classes" @click="scout.sortBy(field)">
        <div class="flex items-center gap-1">
            {{ title }}
            <ArrowDownIcon v-if="showArrowDown" class="h-4 w-4 text-gray-600" />
            <ArrowUpIcon v-if="showArrowUp" class="h-4 w-4 text-gray-600" />
        </div>
    </th>
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';

defineProps({
    id: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: '',
    },
    options: {
        type: Array,
    },
});

const modelValue = defineModel({
    type: Array,
});
</script>

<template>
    <Disclosure as="div" class="border-t border-gray-200 px-4 py-6" v-slot="{ open }">
        <DisclosureButton
            as="h3"
            class="flex w-full items-center justify-between bg-white text-sm text-gray-400 cursor-pointer">
            <div class="flex items-center">
                <span class="font-medium text-gray-900">{{ title }}</span>
                <span
                    v-if="modelValue.length > 0"
                    class="ml-1.5 rounded bg-gray-200 px-1.5 py-0.5 text-xs font-semibold tabular-nums text-gray-700">
                    {{ modelValue.length }}
                </span>
            </div>
            <span class="ml-6 flex items-center">
                <ChevronDownIcon :class="[open ? '-rotate-180' : 'rotate-0', 'h-5 w-5 transform']" aria-hidden="true" />
            </span>
        </DisclosureButton>
        <DisclosurePanel class="pt-6 space-y-6">
            <div v-for="option in options" :key="option.id" class="flex items-center">
                <input
                    :id="`filter-mobile-${id}-${option.id}`"
                    :value="option.id"
                    v-model="modelValue"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" />
                <label :for="`filter-mobile-${id}-${option.id}`" class="ml-3 text-sm text-gray-500 cursor-pointer">
                    {{ option.name }}
                </label>
            </div>
        </DisclosurePanel>
    </Disclosure>
</template>

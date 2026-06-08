<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';
import { computed } from 'vue';

const props = defineProps({
    title: String,
    field: String,
    options: Array,
    scout: Object,
});

const filter = computed(() => props.scout.form.filters[props.field] || []);
</script>

<template>
    <Disclosure as="div" class="border-t border-gray-200 px-4 py-6" v-slot="{ open }">
        <DisclosureButton
            as="h3"
            class="flex w-full cursor-pointer items-center justify-between bg-white text-sm text-gray-400">
            <div class="flex items-center">
                <span class="font-medium text-gray-900">{{ title }}</span>
                <span
                    v-if="filter.length > 0"
                    class="ml-1.5 rounded-sm bg-gray-200 px-1.5 py-0.5 text-xs font-semibold text-gray-700 tabular-nums">
                    {{ filter.length }}
                </span>
            </div>
            <span class="ml-6 flex items-center">
                <ChevronDownIcon :class="[open ? '-rotate-180' : 'rotate-0', 'h-5 w-5 transform']" aria-hidden="true" />
            </span>
        </DisclosureButton>
        <DisclosurePanel class="space-y-6 pt-6">
            <template v-for="option in options" :key="option.id">
                <div class="flex items-center" @click="scout.toggleArrayFilter(field, option.id)">
                    <input
                        :id="`filter-mobile-${field}-${option.id}`"
                        :checked="scout.form.filters[field]?.includes(`${option.id}`)"
                        type="checkbox"
                        class="h-4 w-4 cursor-pointer rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    <label
                        @click.prevent
                        :for="`filter-mobile-${field}-${option.id}`"
                        class="ml-3 cursor-pointer text-sm text-gray-500">
                        {{ option.name }}
                    </label>
                </div>
                <template v-for="child in option.children" :key="child.id">
                    <div class="flex items-center" @click="scout.toggleArrayFilter(field, child.id)">
                        <input
                            :id="`filter-mobile-${field}-${child.id}`"
                            :checked="scout.form.filters[field]?.includes(`${child.id}`)"
                            type="checkbox"
                            class="h-4 w-4 cursor-pointer rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                        <label
                            @click.prevent
                            :for="`filter-mobile-${field}-${child.id}`"
                            class="ml-3 cursor-pointer text-sm text-gray-500">
                            {{ child.name }}
                        </label>
                    </div>
                </template>
            </template>
        </DisclosurePanel>
    </Disclosure>
</template>

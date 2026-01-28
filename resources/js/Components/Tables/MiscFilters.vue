<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';
import { computed } from 'vue';

const props = defineProps({
    scout: Object,
});

const filter = computed(() => {
    if (props.scout.form.filters.business_expenses) {
        return ['business_expenses'];
    }

    return [];
});
</script>

<template>
    <Disclosure as="div" class="border-t border-gray-200 px-4 py-6" v-slot="{ open }">
        <DisclosureButton
            as="h3"
            class="flex w-full items-center justify-between bg-white text-sm text-gray-400 cursor-pointer">
            <div class="flex items-center">
                <span class="font-medium text-gray-900">Misc</span>
                <span
                    v-if="filter.length > 0"
                    class="ml-1.5 rounded-sm bg-gray-200 px-1.5 py-0.5 text-xs font-semibold tabular-nums text-gray-700">
                    {{ filter.length }}
                </span>
            </div>
            <span class="ml-6 flex items-center">
                <ChevronDownIcon :class="[open ? '-rotate-180' : 'rotate-0', 'h-5 w-5 transform']" aria-hidden="true" />
            </span>
        </DisclosureButton>
        <DisclosurePanel class="pt-6 space-y-6">
            <div class="flex items-center" @click="scout.toggleBooleanFilter('business_expenses')">
                <input
                    id="filter-mobile-business-expense"
                    :checked="scout.form.filters.business_expenses"
                    type="checkbox"
                    class="h-4 w-4 rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" />
                <label
                    @click.prevent
                    for="filter-mobile-business-expense"
                    class="ml-3 text-sm text-gray-500 cursor-pointer">
                    Business Expenses
                </label>
            </div>
        </DisclosurePanel>
    </Disclosure>
</template>

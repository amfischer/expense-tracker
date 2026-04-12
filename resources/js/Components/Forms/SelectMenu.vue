<script setup>
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';
import { computed } from 'vue';

const props = defineProps({
    options: Array,
});

const model = defineModel({
    type: Number,
    required: true,
});

const selectedName = computed(() => {
    for (const option of props.options) {
        if (option.id === model.value) {
            return option.name;
        }

        const child = option.children?.find((c) => c.id === model.value);

        if (child) {
            return child.name;
        }
    }

    return '';
});
</script>

<template>
    <Listbox as="div" v-model="model">
        <div class="relative mt-1">
            <ListboxButton
                class="relative w-full cursor-default rounded-md bg-white py-2 pr-10 pl-3 text-left text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset focus:ring-2 focus:ring-indigo-600 focus:outline-hidden sm:text-sm sm:leading-6">
                <span class="block truncate">{{ selectedName }}</span>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </span>
            </ListboxButton>

            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <ListboxOptions
                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm">
                    <template v-for="option in options" :key="option.id">
                        <ListboxOption :value="option.id" v-slot="{ active, selected }">
                            <li
                                :class="[
                                    active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                    'relative cursor-default py-2 pr-9 pl-3 select-none',
                                ]">
                                <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                                    {{ option.name }}
                                </span>
                                <span
                                    v-if="selected"
                                    :class="[
                                        active ? 'text-white' : 'text-indigo-600',
                                        'absolute inset-y-0 right-0 flex items-center pr-4',
                                    ]">
                                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                </span>
                            </li>
                        </ListboxOption>
                        <ListboxOption
                            v-for="child in option.children"
                            :key="child.id"
                            :value="child.id"
                            v-slot="{ active, selected }">
                            <li
                                :class="[
                                    active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                    'relative cursor-default py-2 pr-9 pl-7 select-none',
                                ]">
                                <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                                    {{ child.name }}
                                </span>
                                <span
                                    v-if="selected"
                                    :class="[
                                        active ? 'text-white' : 'text-indigo-600',
                                        'absolute inset-y-0 right-0 flex items-center pr-4',
                                    ]">
                                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                </span>
                            </li>
                        </ListboxOption>
                    </template>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

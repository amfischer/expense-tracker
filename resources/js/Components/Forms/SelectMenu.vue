<script setup>
import { onMounted, ref } from 'vue';
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    options: Array,
});

const model = defineModel({
    type: Number,
    required: true,
});

const getSelectedName = (id) => {
    const selected = props.options.find((option) => option.id === id);
    return selected.name;
};

// const select = ref(null);

// onMounted(() => {
//     if (select.value.hasAttribute('autofocus')) {
//         select.value.focus();
//     }
// });

// defineExpose({ focus: () => select.value.focus() });
</script>

<template>
    <Listbox as="div" v-model="model">
        <div class="relative mt-1">
            <ListboxButton
                class="relative w-full cursor-default rounded-md bg-white py-2 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <span class="block truncate">{{ getSelectedName(model) }}</span>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </span>
            </ListboxButton>

            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <ListboxOptions
                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                    <ListboxOption
                        as="template"
                        v-for="option in options"
                        :key="option.id"
                        :value="option.id"
                        v-slot="{ active, selected }">
                        <li
                            :class="[
                                active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                'relative cursor-default select-none py-2 pl-3 pr-9',
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
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

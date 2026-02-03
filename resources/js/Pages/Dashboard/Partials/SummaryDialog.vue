<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { ArrowLeftIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: '',
    },
    data: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['close']);

const savingsRateClass = computed(() => {
    const rate = props.data?.stats?.savings_rate;
    if (rate === null || rate === undefined) {
        return 'text-gray-400';
    }
    return rate >= 0 ? 'text-emerald-600' : 'text-red-600';
});
</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-40" @close="$emit('close')">
            <TransitionChild
                as="template"
                enter="transition-opacity ease-linear duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="transition-opacity ease-linear duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0">
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 z-40 flex">
                <TransitionChild
                    as="template"
                    enter="transition ease-in-out duration-300 transform"
                    enter-from="translate-x-full"
                    enter-to="translate-x-0"
                    leave="transition ease-in-out duration-300 transform"
                    leave-from="translate-x-0"
                    leave-to="translate-x-full">
                    <DialogPanel class="relative ml-auto flex h-full w-full max-w-md flex-col bg-white shadow-xl">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-4 py-4">
                            <button
                                type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100"
                                @click="$emit('close')">
                                <ArrowLeftIcon class="h-5 w-5" />
                            </button>
                            <h2 class="text-lg font-semibold text-gray-900">{{ label }}</h2>
                            <button
                                type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100"
                                @click="$emit('close')">
                                <XMarkIcon class="h-5 w-5" />
                            </button>
                        </div>

                        <!-- Loading State -->
                        <div v-if="loading" class="flex-1 overflow-y-auto px-4 pb-6">
                            <div class="animate-pulse space-y-4">
                                <div class="flex gap-2">
                                    <div class="h-8 w-24 rounded-full bg-gray-200"></div>
                                    <div class="h-8 w-24 rounded-full bg-gray-200"></div>
                                    <div class="h-8 w-28 rounded-full bg-gray-200"></div>
                                </div>
                                <div class="h-6 w-32 rounded bg-gray-200"></div>
                                <div class="space-y-3">
                                    <div class="h-16 rounded bg-gray-200"></div>
                                    <div class="h-16 rounded bg-gray-200"></div>
                                    <div class="h-16 rounded bg-gray-200"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div v-else-if="data" class="flex-1 overflow-y-auto px-4 pb-6">
                            <!-- Summary Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1.5 text-sm font-medium text-red-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                    </svg>
                                    {{ data.totals.expenses }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                    </svg>
                                    {{ data.totals.income }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700">
                                    <span class="text-gray-500">$</span>
                                    Net: {{ data.totals.difference }}
                                </span>
                            </div>

                            <!-- Transaction Count -->
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1 text-sm text-gray-600">
                                    <svg
                                        class="h-4 w-4 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ data.totals.transaction_count }} transactions
                                </span>
                            </div>

                            <!-- Expenses by Category -->
                            <div v-if="data.expense_categories.length > 0" class="mt-8">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                                    <span>💵</span>
                                    Expenses by Category
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div v-for="category in data.expense_categories" :key="category.name">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900">{{ category.name }}</span>
                                            <span class="text-sm font-semibold text-gray-900">{{ category.total }}</span>
                                        </div>
                                        <div class="relative mt-1.5 h-7 w-full overflow-hidden rounded-md bg-gray-100">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center rounded-md px-2"
                                                :style="{
                                                    width: Math.max(category.percentage, 8) + '%',
                                                    backgroundColor: category.color,
                                                }">
                                                <span class="text-xs font-semibold text-white">
                                                    {{ category.percentage }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Income Sources -->
                            <div v-if="data.income_sources.length > 0" class="mt-8">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                                    <span>💰</span>
                                    Income Sources
                                </h3>
                                <div class="mt-4 divide-y divide-gray-100">
                                    <div
                                        v-for="source in data.income_sources"
                                        :key="source.source"
                                        class="flex items-center justify-between py-3">
                                        <span class="text-sm text-gray-900">{{ source.source }}</span>
                                        <span class="text-sm font-semibold text-emerald-600">{{ source.total }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="mt-8">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                                    <span>📊</span>
                                    Quick Stats
                                </h3>
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <!-- Average Daily -->
                                    <div class="rounded-xl bg-violet-50 p-4">
                                        <p class="text-xs font-medium text-violet-600">Average Daily</p>
                                        <p class="mt-1 text-xl font-bold text-gray-900">
                                            {{ data.stats.avg_daily_spent }}
                                        </p>
                                        <p class="text-xs text-violet-600">spending rate</p>
                                    </div>

                                    <!-- Largest Expense -->
                                    <div class="rounded-xl bg-emerald-50 p-4">
                                        <p class="text-xs font-medium text-emerald-600">Largest Expense</p>
                                        <p class="mt-1 text-xl font-bold text-gray-900">
                                            {{ data.stats.largest_expense?.amount || '-' }}
                                        </p>
                                        <p class="text-xs text-emerald-600">
                                            {{ data.stats.largest_expense?.category || '' }}
                                        </p>
                                    </div>

                                    <!-- Average Daily Income -->
                                    <div class="rounded-xl bg-rose-50 p-4">
                                        <p class="text-xs font-medium text-rose-500">Average Daily</p>
                                        <p class="mt-1 text-xl font-bold text-gray-900">
                                            {{ data.stats.avg_daily_earned }}
                                        </p>
                                        <p class="text-xs text-rose-500">income rate</p>
                                    </div>

                                    <!-- Savings Rate -->
                                    <div class="rounded-xl bg-amber-50 p-4">
                                        <p class="text-xs font-medium text-amber-600">Savings Rate</p>
                                        <p class="mt-1 text-xl font-bold" :class="savingsRateClass">
                                            {{ data.stats.savings_rate !== null ? data.stats.savings_rate + '%' : '-' }}
                                        </p>
                                        <p class="text-xs text-amber-600">of income saved</p>
                                    </div>
                                </div>
                            </div>

                            <!-- View All Button -->
                            <div class="mt-8">
                                <a
                                    :href="route('expenses.index')"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                                    View All Transactions
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="flex flex-1 items-center justify-center px-4 py-6">
                            <p class="text-gray-500">No data available</p>
                        </div>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

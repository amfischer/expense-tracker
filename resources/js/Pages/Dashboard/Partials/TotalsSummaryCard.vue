<script setup>
defineProps({
    report: Object,
});
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-white shadow-sm px-4 py-5 sm:px-6 sm:py-3">
        <div class="flex items-center justify-between">
            <dt class="truncate text-sm font-medium text-gray-500">{{ report.label }}</dt>
            <div
                class="bg-blue-200 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-medium">
                {{ report.count }}
            </div>
        </div>
        <dd class="mt-1 tracking-tight text-gray-900">
            <div class="flex items-center justify-between">
                <span class="text-md">Expenses</span>
                <span class="font-semibold">{{ report.total_expenses }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-md">Incomes</span>
                <span class="font-semibold">{{ report.total_income }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs leading-tight">
                    <button class="rounded-sm border px-2 border-gray-300 text-gray-500 hover:text-white hover:bg-gray-600">
                        View
                    </button>
                </span>
                <span class="font-semibold text-red-500" :class="report.is_loss ? 'text-red-600' : 'text-emerald-600'">
                    {{ report.total_difference }}
                </span>
            </div>
        </dd>
    </div>
</template>

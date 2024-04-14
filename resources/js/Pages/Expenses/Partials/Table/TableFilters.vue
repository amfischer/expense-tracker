<script setup>
import CategoryMenu from './CategoryMenu.vue';
import SortByMenu from './SortByMenu.vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import SearchBox from './SearchBox.vue';

const form = useForm({
    query: '',
    category_ids: [],
    sort_by: 'effective_date',
});

onMounted(() => {
    const url = new URL(window.location.href);
    form.query = url.searchParams.get('query') || '';
});

watch(
    () => form.query,
    () => runSearch(),
);

const runSearch = useDebounceFn(() => {
    form.get(route('expenses.index'), {
        preserveState: true,
        onSuccess: (resp) => {
            console.log('success', resp);
        },
    });
}, 400);

watch([() => form.category_ids, () => form.sort_by], () => applyFilters());

const applyFilters = () => {
    form.get(route('expenses.index'), {
        preserveState: true,
    });
};
</script>

<template>
    <div class="flex flex-col items-end gap-8 md:flex-row md:items-center md:justify-between mb-10">
        <SearchBox v-model="form.query" />
        <div class="flex items-center gap-8">
            <CategoryMenu v-model="form.category_ids" />
            <SortByMenu v-model="form.sort_by" />
        </div>
    </div>
</template>

<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center p-6 bg-gray-100">
        <div class="absolute top-0 left-0">
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="w-full max-w-md p-6 bg-white shadow-md rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>

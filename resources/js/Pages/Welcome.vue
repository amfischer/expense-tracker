<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />

    <div class="relative flex flex-col justify-center items-center min-h-screen bg-gray-100">
        <div v-if="canLogin" class="self-end p-6">
            <template v-if="$page.props.auth.user">
                <Link
                    :href="route('dashboard')"
                    class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Dashboard
                </Link>

                <Link
                    :href="route('logout')"
                    method="post"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Logout
                </Link>
            </template>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Login
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Register
                </Link>
            </template>
        </div>

        <div class="max-w-7xl mx-auto p-6 lg:p-8 grow flex flex-col items-center justify-center">
            <img src="/icons/tree.svg" alt="a tree in black & white" style="width: 45vw" />
            <h1>Expense Tracker</h1>
        </div>
    </div>
</template>

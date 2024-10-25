import { defineStore } from 'pinia';
import { ref } from 'vue';
// import { router } from '@inertiajs/vue3';

export const useAlertStore = defineStore('alert', () => {
    const message = ref('');

    const setMessage = (msg) => {
        message.value = msg;
        setTimeout(() => clear(), 3000);
    };

    const clear = () => (message.value = '');

    // should avoid onMounted() in pinia
    // https://github.com/vuejs/pinia/discussions/1508

    // clear alert if navigating to a new page
    // router.on('finish', () => clear());

    return { message, setMessage, clear };
});

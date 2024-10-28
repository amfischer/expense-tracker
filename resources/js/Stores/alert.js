import { defineStore } from 'pinia';
import { ref } from 'vue';
// import { router } from '@inertiajs/vue3';

export const useAlertStore = defineStore('alert', () => {
    const status = ref('');
    const title = ref('');
    const message = ref('');

    const setSuccessMessage = (msg, titleText = 'Success') => {
        status.value = 'success';
        title.value = titleText;
        message.value = msg;
        toggleAlert();
    };
    const setErrorMessage = (msg, titleText = 'Error') => {
        status.value = 'error';
        title.value = titleText;
        message.value = msg;
        toggleAlert();
    };

    const setMessage = (msg) => {
        message.value = msg;
        setTimeout(() => clear(), 3000);
    };

    const show = ref(false);
    const timeoutId = ref(null);

    /**
     * 1. Show toast and autoclose after 5 seconds
     * 2. If a previous toast is open, close it and toggle open again after 150ms to allow for transition animation
     */
    const toggleAlert = () => {
        if (typeof timeoutId.value === 'number') {
            clearTimeout(timeoutId.value);
            show.value = false;
            setTimeout(() => (show.value = true), 150);
        } else {
            show.value = true;
        }

        timeoutId.value = setTimeout(() => (show.value = false), 500000);
    };

    const hide = () => {
        show.value = false;
        if (typeof timeoutId.value === 'number') {
            clearTimeout(timeoutId.value);
        }
    };

    const clear = () => (message.value = '');

    // should avoid onMounted() in pinia
    // https://github.com/vuejs/pinia/discussions/1508

    // clear alert if navigating to a new page
    // router.on('finish', () => clear());

    return {
        status,
        title,
        message,
        setSuccessMessage,
        setErrorMessage,
        show,
        hide,
        setMessage,
        clear,
    };
});

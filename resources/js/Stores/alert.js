import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useAlertStore = defineStore('alert', () => {
    const message = ref('');

    const setMessage = (msg) => (message.value = msg);

    const clear = () => (message.value = '');

    return { message, setMessage, clear };
});

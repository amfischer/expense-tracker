export function useDateFormatter() {
    const options = {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        timeZone: 'UTC',
    };

    const americanDateTime = new Intl.DateTimeFormat('en-US', options).format;

    const df = (payload) => {
        const date = new Date(payload);
        return americanDateTime(date);
    };

    return {
        df,
    };
}

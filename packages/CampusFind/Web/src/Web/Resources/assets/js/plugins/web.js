export default {
    install(app) {
        app.config.globalProperties.$web = {
            /**
             * Formats date based on locale.
             */
            formatDate: (dateString, locale = 'ar') => {
                try {
                    return new Intl.DateTimeFormat(locale, { dateStyle: 'medium' }).format(new Date(dateString));
                } catch (e) {
                    return dateString;
                }
            },
        };
    },
};

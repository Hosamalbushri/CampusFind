import Flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import { Spanish } from "flatpickr/dist/l10n/es.js";
import { Arabic } from "flatpickr/dist/l10n/ar.js";
import { Persian } from "flatpickr/dist/l10n/fa.js";
import { Turkish } from "flatpickr/dist/l10n/tr.js";

export default {
    install: (app) => {
        window.Flatpickr = Flatpickr;

        const setLocaleFromLang = () => {
            const lang = document.documentElement.getAttribute("lang") || "ar";

            const localeMap = {
                es: Spanish,
                ar: Arabic,
                fa: Persian,
                tr: Turkish,
            };

            const locale = localeMap[lang] || null;

            if (locale && window.Flatpickr && typeof window.Flatpickr.localize === 'function') {
                window.Flatpickr.localize(locale);
            }
        };

        if (typeof window !== 'undefined') {
            window.addEventListener("load", setLocaleFromLang);
            setLocaleFromLang();
        }
    },
};

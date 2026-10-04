/**
 * We are defining all the global rules here and configuring
 * all the `vee-validate` settings for CampusFind Web.
 */
import { configure, defineRule, Field, Form, ErrorMessage } from "vee-validate";
import { localize, setLocale } from "@vee-validate/i18n";
import ar from "@vee-validate/i18n/dist/locale/ar.json";
import en from "@vee-validate/i18n/dist/locale/en.json";
import es from "@vee-validate/i18n/dist/locale/es.json";
import fa from "@vee-validate/i18n/dist/locale/fa.json";
import pt_BR from "@vee-validate/i18n/dist/locale/pt_BR.json";
import tr from "@vee-validate/i18n/dist/locale/tr.json";
import vi from "@vee-validate/i18n/dist/locale/vi.json";
import { all } from "@vee-validate/rules";

window.defineRule = defineRule;

export default {
    install: (app) => {
        /**
         * Global components registration.
         */
        app.component("VForm", Form);
        app.component("VField", Field);
        app.component("VErrorMessage", ErrorMessage);

        window.addEventListener("load", () => {
            const lang = document.documentElement.getAttribute("lang") || "ar";
            setLocale(lang);
        });

        /**
         * Registration of all global validators.
         */
        Object.entries(all).forEach(([name, rule]) => defineRule(name, rule));

        /**
         * Custom phone validation rule.
         */
        defineRule("phone", (value) => {
            if (! value || ! value.length) {
                return true;
            }

            return /^\+?\d+$/.test(value);
        });

        /**
         * Custom address validation rule.
         */
        defineRule("address", (value) => {
            if (! value || ! value.length) {
                return true;
            }

            return /^[a-zA-Z0-9\s.\/*'\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\u0590-\u05FF\u3040-\u309F\u30A0-\u30FF\u0400-\u04FF\u0D80-\u0DFF\u3400-\u4DBF\u2000-\u2A6D\u00C0-\u017F\u0980-\u09FF\u0900-\u097F\u4E00-\u9FFF,\(\)-]{1,60}$/iu.test(
                value
            );
        });

        /**
         * Custom postal code validation rule.
         */
        defineRule("postcode", (value) => {
            if (! value || ! value.length) {
                return true;
            }

            return /^[a-zA-Z0-9][a-zA-Z0-9\s-]*[a-zA-Z0-9]$/.test(value);
        });

        /**
         * Custom decimal validation rule.
         */
        defineRule("decimal", (value, { decimals = '*', separator = '.' } = {}) => {
            if (value === null || value === undefined || value === '') {
                return true;
            }

            if (Number(decimals) === 0) {
                return /^-?\d*$/.test(value);
            }

            const regexPart = decimals === '*' ? '+' : `{1,${decimals}}`;
            const regex = new RegExp(`^[-+]?\\d*(\\${separator}\\d${regexPart})?([eE]{1}[-]?\\d+)?$`);

            return regex.test(value);
        });

        /**
         * Conditional required rule.
         */
        defineRule("required_if", (value, { condition = true } = {}) => {
            if (condition) {
                if (value === null || value === undefined || value === '') {
                    return false;
                }
            }

            return true;
        });

        /**
         * Date format rule (YYYY-MM-DD).
         */
        defineRule("date_format", (value) => {
            if (! value) {
                return true;
            }

            return /^\d{4}-\d{2}-\d{2}$/.test(value);
        });

        /**
         * After today rule.
         */
        defineRule("after", (value) => {
            if (! value) {
                return true;
            }

            const today = new Date();
            const inputDate = new Date(value);

            today.setHours(0, 0, 0, 0);
            inputDate.setHours(0, 0, 0, 0);

            return inputDate >= today;
        });

        defineRule("", () => true);

        configure({
            generateMessage: localize({
                ar: {
                    ...ar,
                    messages: {
                        ...ar.messages,
                        phone: "يجب أن يكون هذا {field} رقم هاتف صالحاً",
                        after: "يجب أن يكون {field} تاريخاً في المستقبل أو اليوم.",
                    },
                },
                en: {
                    ...en,
                    messages: {
                        ...en.messages,
                        phone: "This {field} must be a valid phone number",
                        after: "The {field} must be a date in the future or today.",
                    },
                },
                es: {
                    ...es,
                    messages: {
                        ...es.messages,
                        phone: "Este {field} debe ser un número de teléfono válido.",
                        after: "El {field} debe ser una fecha en el futuro o hoy.",
                    },
                },
                fa: {
                    ...fa,
                    messages: {
                        ...fa.messages,
                        phone: "این {field} باید یک شماره تلفن معتبر باشد.",
                        after: "{field} باید یک تاریخ در آینده یا امروز باشد.",
                    },
                },
                pt_BR: {
                    ...pt_BR,
                    messages: {
                        ...pt_BR.messages,
                    },
                },
                tr: {
                    ...tr,
                    messages: {
                        ...tr.messages,
                        phone: "Bu {field} geçerli bir telefon numarası olmalıdır.",
                        after: "{field} gelecekte veya bugün olmalıdır.",
                    },
                },
                vi: {
                    ...vi,
                    messages: {
                        ...vi.messages,
                    },
                },
            }),
            validateOnBlur: true,
            validateOnInput: true,
            validateOnChange: true,
        });
    },
};

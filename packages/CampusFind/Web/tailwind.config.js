/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./src/Web/Resources/**/*.blade.php",
        "./src/Web/Resources/**/*.js",
    ],

    theme: {
        extend: {
            colors: {
                brandColor: "var(--brand-color, #185c54)",
                refero: {
                    primary: "#185c54",
                    dark: "#134942",
                    mint: "#e6f4ee",
                    mintDark: "#dff3ea",
                    accent: "#15803d",
                },
            },
            fontFamily: {
                cairo: ["Cairo", "sans-serif"],
            },
        },
    },

    darkMode: "class",

    plugins: [],
};

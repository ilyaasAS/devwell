/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.html.twig", // Inclure les templates Twig
    "./src/**/*.js", // Inclure les fichiers JS/TS
    "./public/**/*.html", // Inclure les fichiers HTML statiques si nécessaire
  ],
  theme: {
    extend: {
      colors: {
        primary_dw: "#1A2A40",
        secondary_dw: "#9EFCED",
        tertiary_dw: "#F5F5F5",
        text_dw: "#333333",
      },
    },
  },
  plugins: [],
};


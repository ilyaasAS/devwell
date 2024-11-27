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
        primary_dw: "#1A2A40", // Couleur primaire
        secondary_dw: "#9EFCED", // Couleur secondaire
        tertiary_dw: "#F5F5F5", // Couleur tertiaire
        text_dw: "#333333", // Couleur texte
      },
      boxShadow: {
        'secondary': '0 4px 6px rgba(158, 252, 237, 0.3)', // Ombre personnalisée avec la couleur secondary_dw
      },
    },
  },
  plugins: [],
};

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
        'secondary': '0 10px 30px rgba(158, 252, 237, 0.4)', // Ombre personnalisée plus douce
        'product': '0 10px 20px rgba(158, 252, 237, 0.2), 0 4px 6px rgba(0, 0, 0, 0.1)', // Ombre plus douce pour les produits
        'hover': '0 15px 30px rgba(158, 252, 237, 0.5)', // Ombre au survol
      },
    },
  },
  plugins: [],
};

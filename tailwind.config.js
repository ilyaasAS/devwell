/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.html.twig", // Inclure les templates Twig
    "./src/**/*.js", // Inclure les fichiers JS/TS
    "./public/**/*.html", // Inclure les fichiers HTML statiques si nécessaire
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Roboto', 'sans-serif'], // Définir la police par défaut comme Roboto
      },
      colors: {
        primary_dw: "#1A2A40", // Couleur primaire
        secondary_dw: "#9EFCED", // Couleur secondaire
        tertiary_dw: "#ffffff", // Couleur tertiaire
        text_dw: "#333333", // Couleur texte
      },
      textColor: {
        DEFAULT: '#333333', // Applique text_dw comme couleur par défaut
        black: '#333333',    // Remplace le noir par text_dw
        gray: '#333333',     // Remplace le gris par text_dw
      },
      boxShadow: {
        'secondary': '0 10px 30px rgba(158, 252, 237, 0.4)', // Ombre personnalisée plus douce
        'product': '0 10px 20px rgba(158, 252, 237, 0.2), 0 4px 6px rgba(158, 252, 237, 0.1)', // Ombre plus douce pour les produits
        'hover': '0 15px 30px rgba(158, 252, 237, 0.5)', // Ombre au survol
      },
    },
  },
  plugins: [],
}

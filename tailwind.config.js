// tailwind.config.js
// ⚠️ Tailwind CSS v4 uses CSS-first configuration.
// All theme customization is in: app/src/input.css (@theme block)
// This file is kept only for compatibility with any v3 plugins.

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: "class",
  content: [
    "./app/views/**/*.php",
    "./app/views/**/*.html",
    "./app/reusable/**/*.{php,js}",
    "./app/js/**/*.js",
    "./index.php"
  ],
  plugins: [],
};

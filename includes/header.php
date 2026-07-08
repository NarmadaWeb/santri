<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          "primary": "#004326",
          "primary-container": "#1a5c3a",
          "secondary": "#755b00",
          "secondary-container": "#fed977",
          "background": "#f8faf5",
          "on-surface": "#191c19",
          "outline": "#707971",
          "error": "#ba1a1a",
          "tertiary": "#15412a"
        },
        borderRadius: { "lg": "0.5rem", "xl": "0.75rem" },
        fontFamily: { "headline-md": ["Montserrat"], "body-md": ["Inter"] }
      },
    },
  }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.2); border-radius: 10px; }
    .islamic-pattern {
        background-image: url("https://www.transparenttextures.com/patterns/arabesque.png");
        opacity: 0.05;
    }
</style>

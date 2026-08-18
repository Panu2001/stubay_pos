
                    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
                    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        .fi-theme-switcher { display: none !important; }
                    </style>
                    <script>
                        (function () {
                            localStorage.setItem("theme", "dark");
                            document.documentElement.classList.add("dark");
                            const observer = new MutationObserver(function () {
                                if (!document.documentElement.classList.contains("dark")) {
                                    document.documentElement.classList.add("dark");
                                }
                            });
                            observer.observe(document.documentElement, { attributes: true, attributeFilter: ["class"] });
                        })();
                    </script>
                    

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
                    <?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\storage\framework\views/c6092d72c67785873062bafafce26fe3.blade.php ENDPATH**/ ?>
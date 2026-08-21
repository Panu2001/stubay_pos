
                    <div x-data x-on:print-receipt.window="window.open($event.detail.url, '_blank')" x-on:print-cash-receipt.window="window.open($event.detail.url, '_blank')">
                    </div>
                    <script>
                        window.posRefreshLucideIcons = function () {
                            if (window.lucide) window.lucide.createIcons();
                        };
                        
                        setTimeout(window.posRefreshLucideIcons, 100);
                        document.addEventListener("livewire:navigated", () => window.posRefreshLucideIcons());
                        
                        // Robust observer for any dynamic DOM changes (Livewire 3, Alpine, etc.)
                        const iconObserver = new MutationObserver((mutations) => {
                            let shouldRefresh = false;
                            for (let m of mutations) {
                                if (m.addedNodes.length > 0) {
                                    shouldRefresh = true;
                                    break;
                                }
                            }
                            if (shouldRefresh) {
                                clearTimeout(window.posIconTimeout);
                                window.posIconTimeout = setTimeout(window.posRefreshLucideIcons, 20);
                            }
                        });
                        
                        iconObserver.observe(document.body, { childList: true, subtree: true });
                    </script>
                <?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\storage\framework\views/626fb173b268b2834d3f5c4d77f49352.blade.php ENDPATH**/ ?>
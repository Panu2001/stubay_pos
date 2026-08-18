
                    <div x-data x-on:print-receipt.window="$refs.receiptFrame.src = $event.detail.url" x-on:print-cash-receipt.window="$refs.receiptFrame.src = $event.detail.url">
                        <iframe x-ref="receiptFrame" style="display:none;" src="about:blank"></iframe>
                    </div>
                    <script>
                        window.posRefreshLucideIcons = function (delay = 0) {
                            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, delay);
                        };
                        window.posRefreshLucideIcons(100);
                        document.addEventListener("livewire:navigated", () => window.posRefreshLucideIcons(30));
                        document.addEventListener("livewire:update", () => window.posRefreshLucideIcons(30));
                        document.addEventListener("livewire:updated", () => window.posRefreshLucideIcons(30));
                        document.addEventListener("livewire:morph.updated", () => window.posRefreshLucideIcons(30));
                    </script>
                <?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\storage\framework\views/63d2c884f8aa7a4a761f5abd8e4666a6.blade.php ENDPATH**/ ?>
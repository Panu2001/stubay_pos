
                    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
                    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        .fi-theme-switcher { display: none !important; }
                        button[title="Open navigation menu"],
                        button[title="Close navigation menu"],
                        button[title*="Collapse side panel"],
                        button[title*="Expand side panel"],
                        .fi-topbar-open-sidebar-btn,
                        .fi-topbar-close-sidebar-btn,
                        .fi-topbar-open-collapse-sidebar-btn,
                        .fi-topbar-close-collapse-sidebar-btn,
                        .fi-layout-sidebar-toggle-btn,
                        .fi-sidebar-open-collapse-sidebar-btn,
                        .fi-sidebar-close-collapse-sidebar-btn,
                        .fi-topbar-collapse-sidebar-btn-ctn button {
                            border-radius: 0 !important;
                            border: none !important;
                            box-shadow: none !important;
                            outline: none !important;
                            background-color: transparent !important;
                            background: transparent !important;
                            --tw-ring-shadow: none !important;
                            --tw-shadow: none !important;
                        }
                        .fi-logo,
                        .fi-logo img,
                        .fi-logo-light,
                        .fi-logo-dark,
                        .fi-sidebar-header-logo-ctn,
                        .fi-sidebar-header-logo-ctn img,
                        .fi-sidebar-header-logo-ctn > div,
                        .fi-topbar-start img,
                        .fi-topbar-start > div,
                        .pos-auth-logo,
                        .pos-auth-logo img {
                            border-radius: 0 !important;
                        }
                        .pos-side-panel-scroll {
                            flex: 1 1 0% !important;
                            min-height: 0 !important;
                            max-height: 100% !important;
                            overflow-y: auto !important;
                            overflow-x: hidden !important;
                            overscroll-behavior: contain;
                            -webkit-overflow-scrolling: touch;
                            scrollbar-width: thin;
                            scrollbar-color: rgba(156, 163, 175, 0.4) transparent;
                        }
                        .pos-side-panel-scroll > * {
                            flex-shrink: 0 !important;
                        }
                        .pos-desktop-sidebar {
                            height: 100vh !important;
                            max-height: 100vh !important;
                            width: 16rem !important;
                            min-width: 16rem !important;
                            max-width: 16rem !important;
                            transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1), min-width 300ms cubic-bezier(0.4, 0, 0.2, 1), max-width 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
                            overflow: hidden !important;
                            will-change: width, min-width, max-width;
                        }
                        .pos-desktop-sidebar.pos-sidebar-collapsed {
                            width: 4.5rem !important;
                            min-width: 4.5rem !important;
                            max-width: 4.5rem !important;
                        }
                        .pos-sidebar-label {
                            display: inline-block;
                            max-width: 180px;
                            opacity: 1;
                            transform: translateX(0);
                            transition: opacity 220ms ease, transform 260ms cubic-bezier(0.4, 0, 0.2, 1), max-width 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
                            overflow: hidden !important;
                            text-overflow: clip !important;
                            white-space: nowrap !important;
                        }
                        .pos-sidebar-collapsed .pos-sidebar-label {
                            max-width: 0 !important;
                            opacity: 0 !important;
                            transform: translateX(-12px) !important;
                            pointer-events: none !important;
                        }
                        .pos-sidebar-group-title {
                            max-height: 2rem;
                            opacity: 1;
                            transform: translateX(0);
                            transition: opacity 200ms ease, transform 260ms cubic-bezier(0.4, 0, 0.2, 1), max-height 300ms cubic-bezier(0.4, 0, 0.2, 1), margin 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
                            overflow: hidden !important;
                            white-space: nowrap !important;
                        }
                        .pos-sidebar-collapsed .pos-sidebar-group-title {
                            max-height: 0 !important;
                            opacity: 0 !important;
                            margin-bottom: 0 !important;
                            transform: translateX(-12px) !important;
                            pointer-events: none !important;
                        }
                        .pos-sidebar-badge {
                            opacity: 1;
                            transform: scale(1);
                            transition: opacity 180ms ease, transform 260ms cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .pos-sidebar-collapsed .pos-sidebar-badge {
                            opacity: 0 !important;
                            transform: scale(0.6) !important;
                            pointer-events: none !important;
                        }
                        .pos-sidebar-chevron {
                            transition: transform 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .pos-sidebar-collapsed .pos-sidebar-chevron {
                            transform: rotate(180deg) !important;
                        }
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
                    <?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\storage\framework\views/51b6749bc312128ad87196fafeb6cf6c.blade.php ENDPATH**/ ?>
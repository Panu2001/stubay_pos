<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display - <?php echo e(\App\Models\Setting::get('store_name', 'Store')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;750;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .gradient-bg {
            background: radial-gradient(circle at top right, #fdf2f8, #f0f9ff, #ffffff);
            min-height: 100vh;
        }
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="gradient-bg overflow-hidden text-gray-900">
    <div x-data="customerDisplay()" x-cloak class="h-screen flex flex-col p-4">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100 shrink-0">
            <div class="flex items-center space-x-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Models\Setting::get('store_logo')): ?>
                    <img src="<?php echo e(Storage::url(\App\Models\Setting::get('store_logo'))); ?>" class="h-10 w-auto" alt="Logo">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-gray-900"><?php echo e(\App\Models\Setting::get('store_name', 'Anantha Multi Shop')); ?></h1>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider">Customer Screen</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-black text-emerald-600 tracking-tight" x-text="currency + formatNumber(total)">0.00</div>
                <div class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">Total Bill</div>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="flex-1 grid grid-cols-3 gap-6 overflow-hidden">
            <!-- Left: Compact Cart Items -->
            <div class="col-span-2 flex flex-col overflow-hidden">
                <div class="flex items-center justify-between mb-3 shrink-0">
                    <h2 class="text-sm font-black text-gray-600 uppercase tracking-wider">Shopping Cart</h2>
                    <span class="bg-gray-100 px-2.5 py-0.5 rounded-full text-xs font-bold text-gray-500" x-text="itemsCount + ' Items'">0 Items</span>
                </div>
                
                <!-- Scrollable Item List -->
                <div class="flex-1 overflow-y-auto space-y-2 pr-2 scrollbar-thin">
                    <template x-if="items.length === 0">
                        <div class="h-full flex flex-col items-center justify-center text-center">
                            <div class="w-20 h-20 bg-white rounded-full shadow-lg flex items-center justify-center mb-4 animate-pulse">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-300">Ready to serve you!</h3>
                        </div>
                    </template>

                    <template x-for="item in items" :key="item.id">
                        <div class="glass-card p-3 rounded-xl shadow-sm flex justify-between items-center animate-fadeIn transition-all">
                            <div class="flex items-center space-x-3 min-w-0 flex-1">
                                <!-- Qty Badge -->
                                <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 font-extrabold text-sm shrink-0">
                                    <span x-text="item.quantity"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-gray-800 truncate" x-text="item.name"></h4>
                                    <p class="text-[10px] text-gray-400 font-semibold" x-text="currency + formatNumber(item.price) + ' / ' + (item.unit || 'item')"></p>
                                </div>
                            </div>
                            <div class="text-sm font-extrabold text-gray-900 ml-4 shrink-0" x-text="currency + formatNumber(item.price * item.quantity)"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right: Summary & Status Panel -->
            <div class="flex flex-col space-y-4 overflow-hidden">
                <!-- Summary Card -->
                <div class="bg-white p-4 rounded-2xl shadow-md border border-gray-100 flex-1 flex flex-col justify-between overflow-y-auto">
                    <div>
                        <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Order Summary</h3>
                        <div class="space-y-2.5">
                            <div class="flex justify-between font-semibold text-gray-500 text-xs">
                                <span>Subtotal</span>
                                <span class="text-gray-800" x-text="currency + formatNumber(subtotal)">0.00</span>
                            </div>
                            <div class="flex justify-between font-semibold text-gray-500 text-xs" x-show="discount > 0">
                                <span>Discount</span>
                                <span class="text-green-500" x-text="'-' + currency + formatNumber(discount)">0.00</span>
                            </div>
                            <div class="flex justify-between font-semibold text-gray-500 text-xs">
                                <span>Tax</span>
                                <span class="text-gray-800" x-text="currency + formatNumber(tax)">0.00</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t mt-4 flex justify-between items-end">
                        <span class="text-sm font-bold text-gray-400 uppercase">Total Bill</span>
                        <span class="text-2xl font-black text-gray-900 tracking-tight" x-text="currency + formatNumber(total)">0.00</span>
                    </div>
                </div>

                <!-- Status Card -->
                <div class="p-4 rounded-2xl shadow-md text-center flex flex-col justify-center transition-all duration-500 shrink-0" 
                     :class="status === 'success' ? 'bg-emerald-600 text-white' : 'bg-gray-950 text-white'">
                    
                    <div x-show="status === 'shopping'" class="py-2">
                        <div class="mb-2 flex justify-center">
                            <svg class="w-6 h-6 text-emerald-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h4 class="text-sm font-extrabold mb-0.5 tracking-wide">Adding Items</h4>
                        <p class="text-gray-400 text-[10px] font-semibold">Please check your checkout list</p>
                    </div>

                    <div x-show="status === 'success'" class="py-2">
                        <div class="mb-2 flex justify-center">
                            <svg class="w-8 h-8 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="text-base font-black tracking-wide">Payment Successful!</h4>
                        <p class="text-emerald-100 text-[10px] font-semibold">Thank you for your purchase</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-4 pt-3 border-t border-gray-150 flex justify-between items-center text-[10px] text-gray-400 font-extrabold uppercase tracking-wider shrink-0">
            <p>TERMINAL: #POS-<?php echo e(auth()->id()); ?></p>
            <div class="flex items-center space-x-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="clock"></span>
            </div>
        </div>
    </div>

    <script>
        function customerDisplay() {
            return {
                items: [],
                subtotal: 0,
                tax: 0,
                discount: 0,
                total: 0,
                currency: '<?php echo e(\App\Models\Setting::get('currency', '$')); ?>',
                status: 'shopping', // shopping, payment, success
                itemsCount: 0,

                init() {
                    // Initialize Broadcast Channel
                    const bc = new BroadcastChannel('pos_customer_display');
                    
                    bc.onmessage = (event) => {
                        const data = event.data;
                        this.items = data.cart || [];
                        this.subtotal = data.subtotal || 0;
                        this.tax = data.tax || 0;
                        this.discount = data.discount || 0;
                        this.total = data.total || 0;
                        this.status = data.status || 'shopping';
                        this.itemsCount = Object.keys(this.items).length;

                        if (this.status === 'success') {
                            setTimeout(() => {
                                if (this.status === 'success') {
                                    this.resetDisplay();
                                }
                            }, 5000); // Back to shopping after 5 seconds
                        }
                    };

                    // Clock
                    setInterval(() => {
                        const now = new Date();
                        document.getElementById('clock').innerText = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    }, 1000);
                },

                resetDisplay() {
                    this.items = [];
                    this.subtotal = 0;
                    this.tax = 0;
                    this.discount = 0;
                    this.total = 0;
                    this.status = 'shopping';
                    this.itemsCount = 0;
                },

                formatNumber(num) {
                    return parseFloat(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views\pos\customer-display.blade.php ENDPATH**/ ?>
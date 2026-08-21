@php
    $todayStart = now()->startOfDay();
    $todayEnd = now()->endOfDay();

    $weekStart = now()->startOfWeek();
    $weekEnd = now()->endOfWeek();

    $monthStart = now()->startOfMonth();
    $monthEnd = now()->endOfMonth();

    // 1. Total Order Counts
    $totalOrders = \App\Models\Order::count();

    // 2. Sales Amounts
    $todaySales = \App\Models\Order::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total');
    $weekSales = \App\Models\Order::whereBetween('created_at', [$weekStart, $weekEnd])->sum('total');
    $monthSales = \App\Models\Order::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total');
    $totalSales = \App\Models\Order::sum('total');

    // 3. Total Lends
    $totalLends = \App\Models\Lend::all()->sum(fn ($lend) => $lend->remaining_amount);

    // 4. Cash Drawer
    $drawer = \App\Services\ShiftService::currentStoreDrawer();
    $drawerCashSales = $drawer['cash_sales'];
    $drawerCashIn = $drawer['cash_in'];
    $drawerCashOut = $drawer['cash_out'];
    $drawerCash = $drawer['current'];
    $drawerLabel = 'Current Drawer Cash';
    $drawerDescription = 'Shared store drawer since ' . $drawer['from']->format('M d, H:i');

    // 5. Chart Data is now handled in the Dashboard Livewire Component

    $currency = \App\Models\Setting::get('currency', '$');

    // 5. Product Expiry Lists
    $expiredProducts = \App\Models\Product::whereNotNull('expiry_date')
        ->where('expiry_date', '<', now()->toDateString())
        ->orderBy('expiry_date', 'asc')
        ->get();

    $expiringProducts = \App\Models\Product::whereNotNull('expiry_date')
        ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
        ->orderBy('expiry_date', 'asc')
        ->get();

    // 6. Supplier Purchases
    $allSupplierPurchases = \App\Models\SupplierPurchase::all();
    $supplierTotal = $allSupplierPurchases->sum('total_amount');
    $supplierPaid = $allSupplierPurchases->sum('paid_amount');
    $supplierOwed = $allSupplierPurchases->sum('owed_amount');
    $supplierPaidProgress = $supplierTotal > 0 ? ($supplierPaid / $supplierTotal) * 100 : 0;
    $supplierOwedProgress = $supplierTotal > 0 ? ($supplierOwed / $supplierTotal) * 100 : 0;
@endphp

<div class="space-y-6">
    <div
        class="pos-dashboard-timebar pos-timebar"
        x-data="{
            now: new Date(),
            tick: null,
            init() {
                this.tick = setInterval(() => {
                    this.now = new Date()
                }, 1000)
            },
            get hour() {
                return this.now.getHours()
            },
            get isDay() {
                return this.hour >= 6 && this.hour < 18
            },
            get period() {
                if (this.hour < 5) return 'Late night'
                if (this.hour < 12) return 'Morning'
                if (this.hour < 17) return 'Afternoon'
                if (this.hour < 21) return 'Evening'

                return 'Night'
            },
            get time() {
                return this.now.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                })
            },
            get date() {
                return this.now.toLocaleDateString([], {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric',
                })
            },
            get day() {
                return this.now.toLocaleDateString([], {
                    weekday: 'long',
                })
            },
        }"
        x-bind:class="isDay ? 'is-day' : 'is-night'"
        aria-live="polite"
    >
        <div class="pos-timebar-orbit" aria-hidden="true">
            <span class="pos-timebar-sun"></span>
            <span class="pos-timebar-moon"></span>
            <span class="pos-timebar-star pos-timebar-star-one"></span>
            <span class="pos-timebar-star pos-timebar-star-two"></span>
        </div>

        <div class="pos-timebar-copy">
            <span class="pos-timebar-period" x-text="period"></span>
            <strong x-text="time"></strong>
        </div>

        <div class="pos-timebar-meta">
            <span x-text="day"></span>
            <span x-text="date"></span>
        </div>
    </div>

    <!-- Grid of Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Cash Drawer -->
        <div class="bg-white dark:bg-gray-900/80 border border-emerald-100 dark:border-emerald-500/20 rounded-xl p-6 stat-card shadow-sm flex flex-col justify-between min-h-[170px] transition-colors duration-200">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider font-semibold">{{ $drawerLabel }}</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <i data-lucide="banknote" class="w-4 h-4 text-emerald-500 dark:text-emerald-400"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-wide mt-2">{{ $currency }}{{ number_format($drawerCash, 2) }}</p>
            <span class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $drawerDescription }}</span>
            <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold text-gray-400 dark:text-gray-500">Cash Sales</p>
                    <p class="text-xs font-extrabold text-gray-700 dark:text-gray-200 mt-1">{{ $currency }}{{ number_format($drawerCashSales, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold text-gray-400 dark:text-gray-500">Cash In</p>
                    <p class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $currency }}{{ number_format($drawerCashIn, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wide font-bold text-gray-400 dark:text-gray-500">Cash Out</p>
                    <p class="text-xs font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ $currency }}{{ number_format($drawerCashOut, 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Today's Sales -->
        <div class="bg-white dark:bg-gray-900/80 border border-gray-100 dark:border-gray-800 rounded-xl p-6 stat-card shadow-sm flex flex-col justify-between min-h-[140px] transition-colors duration-200">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider font-semibold">Today's Sales</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <i data-lucide="sun" class="w-4 h-4 text-emerald-500 dark:text-emerald-400"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-wide mt-2">{{ $currency }}{{ number_format($todaySales, 2) }}</p>
            <span class="text-xs text-gray-400 dark:text-gray-500 mt-2">Active sales today</span>
        </div>



        <!-- Total Lends -->
        <div class="bg-white dark:bg-gray-900/80 border border-gray-100 dark:border-gray-800 rounded-xl p-6 stat-card shadow-sm flex flex-col justify-between min-h-[140px] transition-colors duration-200">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider font-semibold">Outstanding Lends</span>
                <div class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4 text-rose-500 dark:text-rose-400"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-rose-600 dark:text-rose-400 tracking-wide mt-2">{{ $currency }}{{ number_format($totalLends, 2) }}</p>
            <span class="text-xs text-gray-400 dark:text-gray-500 mt-2">Pending customer debt</span>
        </div>
        
    </div>

    <!-- Supplier Purchases Widget -->
    <div class="bg-white dark:bg-gray-900/80 border border-gray-100 dark:border-gray-800 rounded-xl p-6 shadow-sm transition-colors duration-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Supplier Purchases Overview</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">Total value of all purchases vs amount paid and pending</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center">
                <i data-lucide="truck" class="w-5 h-5 text-indigo-500 dark:text-indigo-400"></i>
            </div>
        </div>

        <div class="space-y-4 mt-4">
            <div class="flex justify-between text-sm font-medium">
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Total Purchase Value:</span>
                    <span class="text-gray-900 dark:text-white font-bold ml-1">{{ $currency }}{{ number_format($supplierTotal, 2) }}</span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Paid:</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold ml-1">{{ $currency }}{{ number_format($supplierPaid, 2) }}</span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Pending (Owed):</span>
                    <span class="text-rose-600 dark:text-rose-400 font-bold ml-1">{{ $currency }}{{ number_format($supplierOwed, 2) }}</span>
                </div>
            </div>

            <!-- Dual Progress Bar -->
            <div class="w-full bg-gray-100 rounded-full h-3 dark:bg-gray-800 flex overflow-hidden">
                <div class="bg-emerald-500 h-3 transition-all duration-500" style="width: {{ $supplierPaidProgress }}%" title="Paid"></div>
                <div class="bg-rose-500 h-3 transition-all duration-500" style="width: {{ $supplierOwedProgress }}%" title="Pending"></div>
            </div>
            
            <div class="flex justify-between text-xs font-semibold">
                <span class="text-emerald-600 dark:text-emerald-400">{{ number_format($supplierPaidProgress, 1) }}% Paid</span>
                <span class="text-rose-600 dark:text-rose-400">{{ number_format($supplierOwedProgress, 1) }}% Pending</span>
            </div>
        </div>
    </div>

    <!-- Chart Row -->
    <div class="bg-white dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 rounded-xl p-6 shadow-sm transition-colors duration-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white text-lg">Sales Trend</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    @if($salesTrendPeriod === 'today') Hourly revenue for today
                    @elseif($salesTrendPeriod === 'month') Daily revenue for {{ now()->format('F') }}
                    @elseif($salesTrendPeriod === 'year') Monthly revenue for {{ now()->format('Y') }}
                    @else Daily revenue over the last 7 days @endif
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 font-medium hidden sm:inline">Revenue</span>
                </div>
                <x-filament::dropdown placement="bottom-end">
                    <x-slot name="trigger">
                        <button type="button" class="flex items-center justify-between gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-900/50 dark:border-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 w-36 transition-colors">
                            <span>
                                @if($salesTrendPeriod === 'today') Today
                                @elseif($salesTrendPeriod === 'month') This Month
                                @elseif($salesTrendPeriod === 'year') This Year
                                @else This Week @endif
                            </span>
                            <x-filament::icon icon="heroicon-m-chevron-down" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                        </button>
                    </x-slot>

                    <x-filament::dropdown.list>
                        <x-filament::dropdown.list.item wire:click="$set('salesTrendPeriod', 'today')">
                            Today
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item wire:click="$set('salesTrendPeriod', 'week')">
                            This Week
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item wire:click="$set('salesTrendPeriod', 'month')">
                            This Month
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item wire:click="$set('salesTrendPeriod', 'year')">
                            This Year
                        </x-filament::dropdown.list.item>
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            </div>
        </div>
        
        <div class="relative h-[300px] w-full"
             wire:key="sales-chart-{{ $salesTrendPeriod }}"
             x-data="salesChartWidget({ data: @js($chartData), labels: @js($chartLabels), currency: @js($currency) })"
        >
            <canvas x-ref="canvas" wire:ignore></canvas>
        </div>
    </div>

    <!-- Profit Chart Row -->
    <div class="bg-white dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 rounded-xl p-6 shadow-sm transition-colors duration-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white text-lg">Profit Trend</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    @if($profitTrendPeriod === 'today') Hourly profit for today
                    @elseif($profitTrendPeriod === 'month') Daily profit for {{ now()->format('F') }}
                    @elseif($profitTrendPeriod === 'year') Monthly profit for {{ now()->format('Y') }}
                    @else Daily profit over the last 7 days @endif
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 font-medium hidden sm:inline">Profit</span>
                </div>
                <x-filament::dropdown placement="bottom-end">
                    <x-slot name="trigger">
                        <button type="button" class="flex items-center justify-between gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-900/50 dark:border-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-violet-500/50 w-36 transition-colors">
                            <span>
                                @if($profitTrendPeriod === 'today') Today
                                @elseif($profitTrendPeriod === 'month') This Month
                                @elseif($profitTrendPeriod === 'year') This Year
                                @else This Week @endif
                            </span>
                            <x-filament::icon icon="heroicon-m-chevron-down" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                        </button>
                    </x-slot>

                    <x-filament::dropdown.list>
                        <x-filament::dropdown.list.item wire:click="$set('profitTrendPeriod', 'today')">
                            Today
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item wire:click="$set('profitTrendPeriod', 'week')">
                            This Week
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item wire:click="$set('profitTrendPeriod', 'month')">
                            This Month
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item wire:click="$set('profitTrendPeriod', 'year')">
                            This Year
                        </x-filament::dropdown.list.item>
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            </div>
        </div>

        <div class="relative h-[300px] w-full"
             wire:key="profit-chart-{{ $profitTrendPeriod }}"
             x-data="profitChartWidget({ data: @js($profitChartData), labels: @js($profitChartLabels), currency: @js($currency) })"
        >
            <canvas x-ref="canvas" wire:ignore></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('salesChartWidget', (config) => ({
            chart: null,
            init() {
                this.renderChart();
            },
            renderChart() {
                if (this.chart) {
                    this.chart.destroy();
                }

                const ctx = this.$refs.canvas;
                const data = config.data;
                const labels = config.labels;
                const currency = config.currency;

                const isDark = document.documentElement.classList.contains('dark');
                const tickColor = isDark ? '#a1a1aa' : '#4b5563';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                const pointBorder = isDark ? '#09090b' : '#ffffff';
                const tooltipBg = isDark ? '#18181b' : '#ffffff';
                const tooltipText = isDark ? '#f4f4f5' : '#111827';
                const tooltipBorder = isDark ? '#27272a' : '#e5e7eb';

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Sales',
                            data: data,
                            borderColor: '#10b981',
                            borderWidth: 3,
                            backgroundColor: (context) => {
                                const chart = context.chart;
                                const {ctx, chartArea} = chart;
                                if (!chartArea) return null;
                                
                                const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                                gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
                                return gradient;
                            },
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: pointBorder,
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                titleColor: tooltipText,
                                bodyColor: '#10b981',
                                bodyFont: {
                                    weight: 'bold'
                                },
                                borderColor: tooltipBorder,
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return currency + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: gridColor,
                                },
                                ticks: {
                                    color: tickColor,
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    color: gridColor,
                                },
                                ticks: {
                                    color: tickColor,
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    },
                                    callback: function(value) {
                                        return currency + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }));

        Alpine.data('profitChartWidget', (config) => ({
            chart: null,
            init() {
                this.renderChart();
            },
            renderChart() {
                if (this.chart) {
                    this.chart.destroy();
                }

                const ctx = this.$refs.canvas;
                const data = config.data;
                const labels = config.labels;
                const currency = config.currency;

                const isDark = document.documentElement.classList.contains('dark');
                const tickColor = isDark ? '#a1a1aa' : '#4b5563';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                const pointBorder = isDark ? '#09090b' : '#ffffff';
                const tooltipBg = isDark ? '#18181b' : '#ffffff';
                const tooltipText = isDark ? '#f4f4f5' : '#111827';
                const tooltipBorder = isDark ? '#27272a' : '#e5e7eb';

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Profit',
                            data: data,
                            borderColor: '#8b5cf6',
                            borderWidth: 3,
                            backgroundColor: (context) => {
                                const chart = context.chart;
                                const {ctx, chartArea} = chart;
                                if (!chartArea) return null;

                                const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                gradient.addColorStop(0, 'rgba(139, 92, 246, 0.25)');
                                gradient.addColorStop(1, 'rgba(139, 92, 246, 0)');
                                return gradient;
                            },
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#8b5cf6',
                            pointBorderColor: pointBorder,
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                titleColor: tooltipText,
                                bodyColor: '#8b5cf6',
                                bodyFont: {
                                    weight: 'bold'
                                },
                                borderColor: tooltipBorder,
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return currency + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: gridColor,
                                },
                                ticks: {
                                    color: tickColor,
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    color: gridColor,
                                },
                                ticks: {
                                    color: tickColor,
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    },
                                    callback: function(value) {
                                        return currency + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }));
    });
</script>

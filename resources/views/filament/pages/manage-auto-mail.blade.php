<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>
        </div>
    </form>

    <hr class="my-8 border-t border-zinc-200 dark:border-zinc-800" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Daily Report PDF Preview</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">This is a live preview of the PDF report attachment that will be sent via email.</p>
            </div>
            
            <x-filament::button wire:click="downloadPdf" icon="heroicon-o-document-arrow-down" color="info">
                Download PDF
            </x-filament::button>
        </div>

        @php
            $report = $this->getReportData();
        @endphp

        <!-- Simulated Paper Sheet -->
        <div class="max-w-4xl mx-auto rounded-xl overflow-hidden shadow-lg border" style="background-color: #ffffff; border-color: #e5e7eb; color: #333333; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
            <!-- Simulated Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="padding: 24px 32px; border-bottom: 2px solid #10b981; background-color: #f9fafb;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 800; color: #059669; text-transform: uppercase; margin: 0; letter-spacing: 0.05em; line-height: 1.2;">POS Daily Report</h1>
                    <p style="font-size: 12px; color: #666666; margin: 4px 0 0 0;">Generated automatically by POS System</p>
                </div>
                <div class="sm:text-right">
                    <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #999999; letter-spacing: 0.05em; display: block;">Report Date</span>
                    <p style="font-size: 14px; font-weight: 700; color: #333333; margin: 2px 0 0 0;">{{ $report['date'] }}</p>
                </div>
            </div>

            <!-- Simulated Body -->
            <div class="space-y-8" style="padding: 32px;">
                <!-- Stats Grid -->
                <div>
                    <h3 style="font-size: 11px; font-weight: 700; uppercase; text-transform: uppercase; color: #999999; letter-spacing: 0.05em; margin: 0 0 16px 0;">Financial Overview</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Sales -->
                        <div class="rounded-xl border text-center" style="padding: 20px; background-color: #f9fafb; border-color: #f3f4f6;">
                            <span style="display: block; font-size: 11px; font-weight: 600; color: #888888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Total Sales</span>
                            <span style="display: block; font-size: 20px; font-weight: 700; color: #10b981;">${{ number_format($report['todaySales'], 2) }}</span>
                        </div>

                        <!-- Profit -->
                        <div class="rounded-xl border text-center" style="padding: 20px; background-color: #f9fafb; border-color: #f3f4f6;">
                            <span style="display: block; font-size: 11px; font-weight: 600; color: #888888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Total Profit</span>
                            <span style="display: block; font-size: 20px; font-weight: 700; color: #8b5cf6;">${{ number_format($report['todayProfit'], 2) }}</span>
                        </div>

                        <!-- Orders -->
                        <div class="rounded-xl border text-center" style="padding: 20px; background-color: #f9fafb; border-color: #f3f4f6;">
                            <span style="display: block; font-size: 11px; font-weight: 600; color: #888888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Total Orders</span>
                            <span style="display: block; font-size: 20px; font-weight: 700; color: #1f2937;">{{ $report['todayOrdersCount'] }}</span>
                        </div>

                        <!-- Lends -->
                        <div class="rounded-xl border text-center" style="padding: 20px; background-color: #f9fafb; border-color: #f3f4f6;">
                            <span style="display: block; font-size: 11px; font-weight: 600; color: #888888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Lends Today</span>
                            <span style="display: block; font-size: 20px; font-weight: 700; color: #ef4444;">${{ number_format($report['todayLends'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Current Drawer Cash Callout -->
                <div class="rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4" style="padding: 16px; background-color: #ecfdf5; border-left: 4px solid #10b981;">
                    <div>
                        <h4 style="font-size: 12px; font-weight: 700; color: #065f46; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">Current Drawer Cash</h4>
                        <p style="font-size: 12px; color: #047857; margin: 2px 0 0 0; opacity: 0.85;">Total cash currently available in the register</p>
                    </div>
                    <span style="font-size: 18px; font-weight: 800; color: #047857;">${{ number_format($report['currentDrawer'], 2) }}</span>
                </div>

                <!-- Outstanding Callout -->
                <div class="rounded-r-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2" style="padding: 16px; background-color: #fef2f2; border-left: 4px solid #ef4444;">
                    <div>
                        <h4 style="font-size: 12px; font-weight: 700; color: #991b1b; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">Store-wide Outstanding Lends</h4>
                        <p style="font-size: 12px; color: #b91c1c; margin: 2px 0 0 0; opacity: 0.85;">Cumulative unpaid lends across all customers</p>
                    </div>
                    <span style="font-size: 18px; font-weight: 800; color: #b91c1c;">${{ number_format($report['outstandingLends'], 2) }}</span>
                </div>

                <!-- Supplier Purchases Section -->
                <div>
                    <h3 style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #999999; letter-spacing: 0.05em; margin: 24px 0 16px 0;">Supplier Purchases & Owed</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Purchases Today -->
                        <div class="rounded-xl border text-center" style="padding: 20px; background-color: #f9fafb; border-color: #f3f4f6;">
                            <span style="display: block; font-size: 11px; font-weight: 600; color: #888888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Purchases Today</span>
                            <span style="display: block; font-size: 20px; font-weight: 700; color: #3b82f6;">${{ number_format($report['todaySupplierPurchases'], 2) }}</span>
                        </div>

                        <!-- Owed Today -->
                        <div class="rounded-xl border text-center" style="padding: 20px; background-color: #f9fafb; border-color: #f3f4f6;">
                            <span style="display: block; font-size: 11px; font-weight: 600; color: #888888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Owed from Today's Purchases</span>
                            <span style="display: block; font-size: 20px; font-weight: 700; color: #f59e0b;">${{ number_format($report['todaySupplierOwed'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Outstanding Supplier Owed Callout -->
                <div class="rounded-r-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2" style="padding: 16px; background-color: #fffbeb; border-left: 4px solid #f59e0b;">
                    <div>
                        <h4 style="font-size: 12px; font-weight: 700; color: #92400e; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">Total Outstanding Supplier Owed</h4>
                        <p style="font-size: 12px; color: #d97706; margin: 2px 0 0 0; opacity: 0.85;">Cumulative unpaid balance to all suppliers</p>
                    </div>
                    <span style="font-size: 18px; font-weight: 800; color: #d97706;">${{ number_format($report['outstandingSupplierOwed'], 2) }}</span>
                </div>


            </div>

            <!-- Simulated Footer -->
            <div class="text-center text-xs" style="padding: 16px; background-color: #f9fafb; border-top: 1px solid #e5e7eb; color: #999999;">
                POS System &copy; {{ date('Y') }} &bull; Confidential Admin Report
            </div>
        </div>
    </div>
</x-filament-panels::page>

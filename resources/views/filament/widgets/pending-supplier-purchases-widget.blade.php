<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">Pending Supplier Purchases</h2>
            <span class="text-sm text-gray-500">Unsettled Invoices</span>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between text-sm font-medium">
                <div>
                    <span class="text-gray-500">Total Purchase Value:</span>
                    <span class="text-gray-900 dark:text-white font-bold ml-1">${{ number_format($totalAmount, 2) }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Amount Paid:</span>
                    <span class="text-success-600 dark:text-success-400 font-bold ml-1">${{ number_format($paidAmount, 2) }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Amount Owed:</span>
                    <span class="text-danger-600 dark:text-danger-400 font-bold ml-1">${{ number_format($owedAmount, 2) }}</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700 relative overflow-hidden">
                <div class="bg-primary-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
            </div>
            
            <div class="flex justify-between text-xs text-gray-500">
                <span>{{ number_format($progress, 1) }}% Paid</span>
                <span>{{ number_format(100 - $progress, 1) }}% Owed</span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

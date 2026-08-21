<?php

namespace App\Console\Commands;

use App\Mail\DailyAdminReportMail;
use App\Models\Lend;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAdminReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-admin-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily sales, profit, and lends report to all admins with a PDF attachment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // 1. Total Orders
        $todayOrdersCount = Order::whereBetween('created_at', [$todayStart, $todayEnd])->count();

        // 2. Sales
        $todaySales = Order::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total');

        // 3. Profit
        $todayProfit = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
            ->where('order_items.unit_cost_price', '>', 0)
            ->selectRaw('SUM((order_items.unit_price - order_items.unit_cost_price) * order_items.quantity) as profit')
            ->value('profit') ?? 0;

        // 4. Lends
        $todayLends = Lend::whereBetween('created_at', [$todayStart, $todayEnd])->sum('remaining_amount');
        $outstandingLends = Lend::sum('remaining_amount');

        // 5. Supplier Purchases & Owed Amounts
        $todaySupplierPurchases = \App\Models\SupplierPurchase::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total_amount');
        $todaySupplierOwed = \App\Models\SupplierPurchase::whereBetween('created_at', [$todayStart, $todayEnd])->sum('owed_amount');
        $outstandingSupplierOwed = \App\Models\SupplierPurchase::sum('owed_amount');

        // Current Drawer Cash
        $currentDrawer = \App\Services\ShiftService::currentStoreDrawer()['current'];

        $data = [
            'date' => now()->format('Y-m-d'),
            'todayOrdersCount' => $todayOrdersCount,
            'todaySales' => $todaySales,
            'todayProfit' => $todayProfit,
            'todayLends' => $todayLends,
            'outstandingLends' => $outstandingLends,
            'todaySupplierPurchases' => $todaySupplierPurchases,
            'todaySupplierOwed' => $todaySupplierOwed,
            'outstandingSupplierOwed' => $outstandingSupplierOwed,
            'currentDrawer' => $currentDrawer,
        ];

        // 5. Send to Admins and Additional Emails
        $admins = User::where('role', 'admin')->get();
        $emails = $admins->pluck('email')->toArray();

        $additionalEmailsJson = \App\Models\Setting::get('additional_report_emails', '[]');
        $additionalEmails = json_decode($additionalEmailsJson, true) ?? [];

        $allEmails = array_unique(array_merge($emails, $additionalEmails));

        if (empty($allEmails)) {
            $this->info('No recipients found.');
            return;
        }

        foreach ($allEmails as $email) {
            Mail::to($email)->send(new DailyAdminReportMail($data));
        }

        $this->info('Daily report pushed to admins successfully.');
    }
}

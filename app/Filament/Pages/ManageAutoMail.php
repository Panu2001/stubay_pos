<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Section;
use App\Models\Setting;
use Filament\Notifications\Notification;

class ManageAutoMail extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 10;
    protected string $view = 'filament.pages.manage-auto-mail';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected static ?string $title = 'Auto Mail Setup';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'enable_daily_report' => (bool) Setting::get('enable_daily_report', true),
            'daily_report_time' => Setting::get('daily_report_time', '23:50'),
            'additional_report_emails' => json_decode(Setting::get('additional_report_emails', '[]'), true) ?? [],
        ]);
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Section::make('Daily Admin Report Configuration')
                    ->description('Manage how and when the daily sales, profit, and lends report is sent.')
                    ->schema([
                        Toggle::make('enable_daily_report')
                            ->label('Enable Daily Report Email')
                            ->helperText('If disabled, the automated daily report will not be sent.')
                            ->default(true),

                        TimePicker::make('daily_report_time')
                            ->label('Scheduled Time')
                            ->helperText('The exact time the email should be sent each day.')
                            ->default('23:50')
                            ->required(),

                        TagsInput::make('additional_report_emails')
                            ->label('Additional Recipients')
                            ->helperText('All Admins receive this by default. Add any other email addresses here.')
                            ->placeholder('Type an email and press Enter')
                            ->nestedRecursiveRules([
                                'email'
                            ]),
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('enable_daily_report', $data['enable_daily_report']);
        Setting::set('daily_report_time', $data['daily_report_time']);
        Setting::set('additional_report_emails', json_encode($data['additional_report_emails'] ?? []));

        Notification::make()
            ->title('Auto-Mail Settings Saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('downloadPdf')
                ->label('Download PDF Report')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(fn () => $this->downloadPdf()),
        ];
    }

    public function getReportData(): array
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // 1. Total Orders
        $todayOrdersCount = \App\Models\Order::whereBetween('created_at', [$todayStart, $todayEnd])->count();

        // 2. Sales
        $todaySales = \App\Models\Order::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total');

        // 3. Profit
        $todayProfit = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
            ->where('order_items.unit_cost_price', '>', 0)
            ->selectRaw('SUM((order_items.unit_price - order_items.unit_cost_price) * order_items.quantity) as profit')
            ->value('profit') ?? 0;

        // 4. Lends
        $todayLends = \App\Models\Lend::whereBetween('created_at', [$todayStart, $todayEnd])->sum('remaining_amount');
        $outstandingLends = \App\Models\Lend::sum('remaining_amount');

        // 5. Supplier Purchases & Owed Amounts
        $todaySupplierPurchases = \App\Models\SupplierPurchase::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total_amount');
        $todaySupplierOwed = \App\Models\SupplierPurchase::whereBetween('created_at', [$todayStart, $todayEnd])->sum('owed_amount');
        $outstandingSupplierOwed = \App\Models\SupplierPurchase::sum('owed_amount');

        // Current Drawer Cash
        $currentDrawer = \App\Services\ShiftService::currentStoreDrawer()['current'];

        return [
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
    }

    public function downloadPdf()
    {
        $reportData = $this->getReportData();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.daily_report', $reportData);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Daily_Report_' . $reportData['date'] . '.pdf'
        );
    }
}

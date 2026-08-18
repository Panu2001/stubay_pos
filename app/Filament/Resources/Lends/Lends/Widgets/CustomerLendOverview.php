<?php

namespace App\Filament\Resources\Lends\Lends\Widgets;

use App\Models\Customer;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class CustomerLendOverview extends StatsOverviewWidget
{
    public ?Customer $record = null;

    protected int | string | array $columnSpan = 'full';

    #[On('lends-settled')]
    public function refreshStats(): void
    {
        //
    }

    protected function getStats(): array
    {
        $currency = Setting::get('currency', '$');
        $lends = $this->record?->lends() ?? null;

        $lendRecords = $lends ? (clone $lends)->get() : collect();
        $totalLends = (float) $lendRecords->sum('total_amount');
        $totalPaid = (float) $lendRecords->sum('paid_amount');
        $outstanding = (float) $lendRecords->sum(fn ($lend) => $lend->remaining_amount);

        $status = match (true) {
            $totalLends <= 0 => 'No Lends',
            $outstanding <= 0 => 'Settled',
            $totalPaid > 0 => 'Partially Settled',
            default => 'Not Settled',
        };

        return [
            Stat::make('Total Lends', $currency . number_format($totalLends, 2))
                ->description('All lend amounts for this customer')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),
            Stat::make('Settled Amount', $currency . number_format($totalPaid, 2))
                ->description('Total paid against lends')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($totalPaid > 0 ? 'success' : 'gray'),
            Stat::make('Outstanding Balance', $currency . number_format($outstanding, 2))
                ->description($status)
                ->descriptionIcon($outstanding > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->color($outstanding > 0 ? 'warning' : 'success'),
        ];
    }
}

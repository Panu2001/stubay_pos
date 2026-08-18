<?php

namespace App\Filament\Resources\Lends\Lends\Pages;

use App\Filament\Resources\Lends\Lends\LendResource;
use App\Filament\Resources\Lends\Lends\Widgets\CustomerLendOverview;
use App\Models\CashEntry;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ViewCustomerLends extends EditRecord
{
    protected static string $resource = LendResource::class;

    protected static ?string $title = 'Customer Lends';

    // No form save — this is a view-only detail page
    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // We don't actually have a form to fill; suppress it gracefully
        return $data;
    }

    #[On('lends-settled')]
    public function refreshLendSummary(): void
    {
        //
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        $currency = Setting::get('currency', '$');

        $outstanding = $this->getOutstandingBalance();

        return [
            Action::make('toggle_history')
                ->label('Show History')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->url(fn () => LendResource::getUrl('history', ['record' => $this->getRecord()])),

            Action::make('edit_last_settlement')
                ->label('Edit Last Settled Amount')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->modalHeading('Edit Last Settled Amount')
                ->modalDescription('Correct only the last settled payment. Order amounts and lend amounts will not be changed.')
                ->modalWidth('xl')
                ->form([
                    TextInput::make('previous_amount')
                        ->label('Previously Settled Amount')
                        ->prefix($currency)
                        ->numeric()
                        ->required()
                        ->default(fn () => $this->getLatestCashSettlementAmount()),
                    TextInput::make('correct_amount')
                        ->label('Correct Settled Amount')
                        ->prefix($currency)
                        ->numeric()
                        ->required()
                        ->default(fn () => $this->getLatestCashSettlementAmount())
                        ->rules([
                            fn (callable $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                $previousAmount = round((float) $get('previous_amount'), 2);
                                $correctAmount = round((float) $value, 2);
                                $totalPaid = $this->getTotalPaidAmount();
                                $outstanding = $this->getOutstandingBalance();

                                if ($previousAmount <= 0) {
                                    $fail('Previously settled amount must be greater than zero.');
                                }

                                if ($previousAmount > $totalPaid) {
                                    $fail('Previously settled amount cannot be higher than total paid amount of ' . number_format($totalPaid, 2) . '.');
                                }

                                if ($correctAmount < 0) {
                                    $fail('Correct settled amount cannot be negative.');
                                }

                                if ($correctAmount > ($previousAmount + $outstanding)) {
                                    $fail('Correct settled amount cannot exceed the available outstanding balance.');
                                }
                            },
                        ]),
                    Textarea::make('notes')
                        ->label('Correction Notes')
                        ->placeholder('Optional reason for correcting the last settlement...'),
                ])
                ->action(function (array $data) use ($currency) {
                    $record = $this->getRecord();
                    $previousAmount = round((float) $data['previous_amount'], 2);
                    $correctAmount = round((float) $data['correct_amount'], 2);

                    DB::transaction(function () use ($record, $previousAmount, $correctAmount, $data) {
                        $this->removeSettledAmount($record, $previousAmount);
                        $this->applySettledAmount($record, $correctAmount);

                        $latestCashSettlement = $this->getLatestCashSettlement();

                        if ($latestCashSettlement) {
                            $latestCashSettlement->update([
                                'amount' => $correctAmount,
                                'notes' => trim(($latestCashSettlement->notes ? $latestCashSettlement->notes . "\n" : '') . 'Corrected last settlement. ' . ($data['notes'] ?? '')),
                            ]);
                        }
                    });

                    Notification::make()
                        ->title('Last Settled Amount Updated')
                        ->body('Changed from ' . $currency . number_format($previousAmount, 2) . ' to ' . $currency . number_format($correctAmount, 2) . '.')
                        ->success()
                        ->send();

                    $this->dispatch('lends-settled');
                }),

            Action::make('settle_lends')
                ->label('Settle Lends')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn () => $this->getOutstandingBalance() > 0)
                ->form([
                    TextInput::make('payment_amount')
                        ->label('Settled Amount')
                        ->prefix($currency)
                        ->numeric()
                        ->required()
                        ->default(fn () => $this->getOutstandingBalance())
                        ->rules([
                            fn () => function (string $attribute, $value, \Closure $fail) {
                                $outstanding = $this->getOutstandingBalance();

                                if ((float) $value <= 0) {
                                    $fail('Settled amount must be greater than zero.');
                                }

                                if ((float) $value > $outstanding) {
                                    $fail('Settled amount cannot exceed outstanding balance of ' . number_format($outstanding, 2) . '.');
                                }
                            },
                        ]),
                    Select::make('payment_method')
                        ->label('Payment Method')
                        ->options([
                            'cash' => 'Cash',
                            'card' => 'Card',
                            'bank_transfer' => 'Bank Transfer',
                        ])
                        ->default('cash')
                        ->required(),
                    Textarea::make('notes')
                        ->label('Payment Notes')
                        ->placeholder('Optional payment details...'),
                ])
                ->action(function (array $data) use ($currency) {
                    $record = $this->getRecord();
                    $paymentAmount = round((float) $data['payment_amount'], 2);
                    $remainingPayment = $paymentAmount;

                    DB::transaction(function () use ($record, $data, $paymentAmount, &$remainingPayment) {
                        $lends = $record->lends()
                            ->whereColumn('paid_amount', '<', 'total_amount')
                            ->oldest()
                            ->lockForUpdate()
                            ->get();

                        foreach ($lends as $lend) {
                            if ($remainingPayment <= 0) {
                                break;
                            }

                            $remainingBalance = $lend->remaining_amount;
                            $allocation = min($remainingPayment, $remainingBalance);

                            if ($allocation <= 0) {
                                continue;
                            }

                            $lend->paid_amount = round((float) $lend->paid_amount + $allocation, 2);
                            $lend->save();

                            $remainingPayment = round($remainingPayment - $allocation, 2);
                        }

                        if ($data['payment_method'] === 'cash') {
                            CashEntry::create([
                                'user_id' => auth()->id(),
                                'type' => 'in',
                                'amount' => $paymentAmount,
                                'reason' => 'Lend payment from customer: ' . $record->name,
                                'notes' => 'Customer lend settlement. ' . ($data['notes'] ?? ''),
                            ]);
                        }
                    });

                    $newOutstanding = $this->getOutstandingBalance();

                    Notification::make()
                        ->title($newOutstanding <= 0 ? 'Lends Settled' : 'Lends Partially Settled')
                        ->body('Recorded ' . $currency . number_format($paymentAmount, 2) . ' for ' . $record->name . '.')
                        ->success()
                        ->send();

                    $this->dispatch('lends-settled');
                }),

            Action::make('print_statement')
                ->label('Print Statement')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(route('customer.print-lends', $record->id))
                ->openUrlInNewTab(),

            Action::make('outstanding_badge')
                ->label('Outstanding: '.$currency.number_format($outstanding, 2))
                ->icon('heroicon-o-banknotes')
                ->color($outstanding > 0 ? 'danger' : 'success')
                ->disabled(),
        ];
    }

    public function getRelationManagers(): array
    {
        return [];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.resources.lends.customer-lends-overview')
                    ->viewData(fn (): array => $this->getCustomerLendsOverviewData()),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerLendOverview::class,
        ];
    }

    private function getLatestCashSettlement(): ?CashEntry
    {
        return CashEntry::query()
            ->where('type', 'in')
            ->where('reason', 'Lend payment from customer: ' . $this->getRecord()->name)
            ->latest()
            ->first();
    }

    private function getLatestCashSettlementAmount(): float
    {
        return round((float) ($this->getLatestCashSettlement()?->amount ?? 0), 2);
    }

    private function getTotalPaidAmount(): float
    {
        return round((float) $this->getRecord()->lends()->sum('paid_amount'), 2);
    }

    private function removeSettledAmount($record, float $amount): void
    {
        $remaining = round($amount, 2);

        $lends = $record->lends()
            ->where('paid_amount', '>', 0)
            ->latest()
            ->lockForUpdate()
            ->get();

        foreach ($lends as $lend) {
            if ($remaining <= 0) {
                break;
            }

            $deduction = min($remaining, (float) $lend->paid_amount);
            $lend->paid_amount = round((float) $lend->paid_amount - $deduction, 2);
            $lend->save();

            $remaining = round($remaining - $deduction, 2);
        }
    }

    private function applySettledAmount($record, float $amount): void
    {
        $remaining = round($amount, 2);

        $lends = $record->lends()
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->oldest()
            ->lockForUpdate()
            ->get();

        foreach ($lends as $lend) {
            if ($remaining <= 0) {
                break;
            }

            $allocation = min($remaining, $lend->remaining_amount);
            $lend->paid_amount = round((float) $lend->paid_amount + $allocation, 2);
            $lend->save();

            $remaining = round($remaining - $allocation, 2);
        }
    }

    private function getOutstandingBalance(): float
    {
        return (float) $this->getRecord()
            ->lends()
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->get()
            ->sum(fn ($lend) => $lend->remaining_amount);
    }

    private function getCustomerLendsOverviewData(): array
    {
        $customer = $this->getRecord();
        $lends = $customer
            ->lends()
            ->with(['order.items'])
            ->orderByDesc('created_at')
            ->get();

        $totalLends = 0;
        $totalPaid = 0;
        $outstanding = 0;
        $openLends = 0;

        foreach ($lends as $lend) {
            $totalLends += (float) $lend->total_amount;
            $totalPaid += (float) $lend->paid_amount;
            $outstanding += (float) $lend->remaining_amount;

            if ($lend->status !== 'paid') {
                $openLends++;
            }
        }

        $latestSettlement = $this->getLatestCashSettlement();

        return [
            'customer' => $customer,
            'currency' => Setting::get('currency', '$'),
            'lends' => $lends,
            'openLends' => $lends->filter(fn ($lend) => $lend->status !== 'paid')->values(),
            'recentLends' => $lends->take(8),
            'latestSettlement' => $latestSettlement,
            'stats' => [
                'total_lends' => $totalLends,
                'total_paid' => $totalPaid,
                'outstanding' => $outstanding,
                'open_lends' => $openLends,
                'lend_count' => $lends->count(),
                'progress' => $totalLends > 0 ? min(100, round(($totalPaid / $totalLends) * 100)) : 0,
            ],
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Lend;
use App\Models\Setting;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class OutstandingLendsTable extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Outstanding Lends';

    public function table(Table $table): Table
    {
        $currency = Setting::get('currency', '$');

        return $table
            ->query(
                Lend::query()
                    ->where('status', '!=', 'paid')
                    ->latest()
            )
            ->description(fn ($livewire) => new \Illuminate\Support\HtmlString(
                '<span class="text-sm font-medium">Total Outstanding: <span class="text-rose-500 font-bold">' . 
                $currency . number_format((float) $livewire->getFilteredTableQuery()->sum(\Illuminate\Support\Facades\DB::raw('total_amount - paid_amount')), 2) . 
                '</span></span>'
            ))
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->prefix($currency)
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Paid Amount')
                    ->prefix($currency)
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('Outstanding')
                    ->prefix($currency)
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Select::make('period')
                            ->options([
                                'today' => 'Today',
                                'week' => 'This Week',
                                'month' => 'This Month',
                                'year' => 'This Year',
                                'custom_date' => 'Custom Date',
                                'custom_duration' => 'Custom Duration',
                            ])
                            ->label('Filter Option')
                            ->live(),
                        DatePicker::make('specific_date')
                            ->label('Specific Date')
                            ->visible(fn ($get): bool => $get('period') === 'custom_date'),
                        DatePicker::make('created_from')
                            ->label('From Date')
                            ->visible(fn ($get): bool => $get('period') === 'custom_duration'),
                        DatePicker::make('created_until')
                            ->label('To Date')
                            ->visible(fn ($get): bool => $get('period') === 'custom_duration'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['period'] ?? null,
                                function (Builder $query, $period) use ($data) {
                                    return match ($period) {
                                        'today' => $query->whereDate('created_at', today()),
                                        'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                                        'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                                        'year' => $query->whereYear('created_at', now()->year),
                                        'custom_date' => $query->when(
                                            $data['specific_date'] ?? null,
                                            fn (Builder $q, $date) => $q->whereDate('created_at', $date)
                                        ),
                                        'custom_duration' => $query
                                            ->when(
                                                $data['created_from'] ?? null,
                                                fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date)
                                            )
                                            ->when(
                                                $data['created_until'] ?? null,
                                                fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date)
                                            ),
                                        default => $query,
                                    };
                                }
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (($data['period'] ?? null) && !in_array($data['period'], ['custom_date', 'custom_duration'])) {
                            $indicators[] = \Filament\Tables\Filters\Indicator::make('Period: ' . ucfirst($data['period']))
                                ->removeField('period');
                        }
                        if (($data['period'] ?? null) === 'custom_date' && ($data['specific_date'] ?? null)) {
                            $indicators[] = \Filament\Tables\Filters\Indicator::make('Date: ' . Carbon::parse($data['specific_date'])->toFormattedDateString())
                                ->removeField('specific_date')
                                ->removeField('period');
                        }
                        if (($data['period'] ?? null) === 'custom_duration') {
                            if ($data['created_from'] ?? null) {
                                $indicators[] = \Filament\Tables\Filters\Indicator::make('From: ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                    ->removeField('created_from')
                                    ->removeField('period');
                            }
                            if ($data['created_until'] ?? null) {
                                $indicators[] = \Filament\Tables\Filters\Indicator::make('To: ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                    ->removeField('created_until')
                                    ->removeField('period');
                            }
                        }
                        return $indicators;
                    })
            ]);
    }
}

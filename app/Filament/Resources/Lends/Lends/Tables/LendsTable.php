<?php

namespace App\Filament\Resources\Lends\Lends\Tables;

use App\Models\Customer;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LendsTable
{
    public static function configure(Table $table): Table
    {
        $currency = Setting::get('currency', '$');

        return $table
            ->query(
                // Only show customers who have at least one unpaid/partially paid lend
                Customer::query()->whereHas('lends', fn ($q) => $q->where('status', '!=', 'paid'))
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('outstanding')
                    ->label('Outstanding Balance')
                    ->getStateUsing(fn ($record) => $record->lends()
                        ->where('status', '!=', 'paid')
                        ->get()
                        ->sum(fn ($l) => $l->remaining_amount))
                    ->prefix($currency)
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->color('danger')
                    ->sortable(false),
                TextColumn::make('lend_count')
                    ->label('Open Lends')
                    ->getStateUsing(fn ($record) => $record->lends()->where('status', '!=', 'paid')->count())
                    ->badge()
                    ->color('warning')
                    ->sortable(false),
            ])
            ->filters([])
            ->headerActions([
                Action::make('print_shop_statement')
                    ->label('Print Shop Summary')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(route('shop.print-lends'))
                    ->openUrlInNewTab(),
                Action::make('print_customer_statement')
                    ->label('Print Customer Statement')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->modalHeading('Print Customer Statement')
                    ->modalWidth('md')
                    ->modalSubmitActionLabel('Open Statement')
                    ->form([
                        \Filament\Forms\Components\Select::make('customer_id')
                            ->label('Select Customer')
                            ->options(fn () => Customer::query()
                                ->whereHas('lends')
                                ->orderBy('name')
                                ->limit(25)
                                ->pluck('name', 'id'))
                            ->getSearchResultsUsing(fn (string $search): array => Customer::query()
                                ->whereHas('lends')
                                ->where('name', 'like', "%{$search}%")
                                ->orderBy('name')
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelUsing(fn ($value): ?string => Customer::find($value)?->name)
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('customer.print-lends', $data['customer_id']);
                    })
                    ->openUrlInNewTab(),
            ])
            ->recordActions([
                Action::make('view_lend_history')
                    ->label('View / Edit Payments')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->url(fn ($record) => \App\Filament\Resources\Lends\Lends\LendResource::getUrl('edit', ['record' => $record])),
                Action::make('print_statement')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn ($record) => route('customer.print-lends', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([])
            ->emptyStateHeading('No Outstanding Lends')
            ->emptyStateDescription('All customers have settled their payments.')
            ->emptyStateIcon('heroicon-o-check-badge');
    }
}

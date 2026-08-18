<?php

namespace App\Filament\Customer\Resources\Lends\Lends\Schemas;

use Filament\Schemas\Schema;

class LendInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\TextEntry::make('order.order_number')->label('Order #')->weight('bold'),
                \Filament\Infolists\Components\TextEntry::make('status')->badge(),
                \Filament\Infolists\Components\TextEntry::make('due_date')->date(),
                \Filament\Infolists\Components\TextEntry::make('total_amount')->label('Total Amount')->formatStateUsing(fn ($state) => \App\Models\Setting::get('currency', '$') . number_format($state, 2)),
                \Filament\Infolists\Components\TextEntry::make('paid_amount')->label('Paid Amount')->formatStateUsing(fn ($state) => \App\Models\Setting::get('currency', '$') . number_format($state, 2)),
                \Filament\Infolists\Components\TextEntry::make('notes')->label('Admin Notes')->columnSpanFull(),
            ]);
    }
}

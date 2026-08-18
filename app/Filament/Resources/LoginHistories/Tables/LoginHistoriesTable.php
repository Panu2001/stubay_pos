<?php

namespace App\Filament\Resources\LoginHistories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoginHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Staff Name')->searchable(),
                TextColumn::make('ip_address')->label('IP Address')->searchable(),
                TextColumn::make('user_agent')->label('Device / Browser')->limit(30),
                TextColumn::make('login_at')->label('Login Time')->dateTime()->sortable(),
            ])
            ->defaultSort('login_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

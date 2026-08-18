<?php

namespace App\Filament\Resources\CashEntryResource\Pages;

use App\Filament\Resources\CashEntryResource;
use App\Models\Setting;
use App\Services\ShiftService;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageCashEntries extends ManageRecords
{
    protected static string $resource = CashEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function ($record) {
                    $this->dispatch('print-cash-receipt', url: route('cash-entry-receipt', $record));
                }),
            Actions\Action::make('setDrawerCash')
                ->label('Set Current Drawer Cash')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->visible(fn () => auth()->user()?->isAdmin())
                ->modalHeading('Set Current Drawer Cash')
                ->modalDescription('This will reset the shared drawer cash from this moment. Previous cash movement will not be added again.')
                ->modalSubmitActionLabel('Set Drawer Cash')
                ->form([
                    TextInput::make('current_cash')
                        ->label('Current Drawer Cash')
                        ->numeric()
                        ->prefix(Setting::get('currency', '$'))
                        ->default(fn () => number_format((float) ShiftService::currentStoreDrawer()['current'], 2, '.', ''))
                        ->required(),
                ])
                ->action(function (array $data) {
                    ShiftService::setStoreDrawer(auth()->user(), (float) $data['current_cash']);

                    Notification::make()
                        ->title('Drawer Cash Updated')
                        ->body('The shared drawer cash has been updated for all dashboard users.')
                        ->success()
                        ->send();
                }),
        ];
    }
}

<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required(),
                TextInput::make('nic_number')
                    ->label('NIC Number')
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('monthly_lend_limit')
                    ->label('Monthly Lend Limit')
                    ->numeric()
                    ->default(25000)
                    ->prefix('LKR')
                    ->helperText('The maximum outstanding lend balance allowed for this customer.')
                    ->rules([
                        fn (?\Illuminate\Database\Eloquent\Model $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($record) {
                            $user = auth()->user();
                            if ($user && !$user->isAdmin()) {
                                $originalLimit = $record ? (float) $record->getOriginal('monthly_lend_limit') : 0;
                                $newValue = (float) $value;
                                
                                if ($newValue > 60000 && $newValue > $originalLimit) {
                                    $fail('Only administrators can increase the lend limit above 60,000 LKR.');
                                }
                            }
                        },
                    ]),
                \Filament\Forms\Components\Textarea::make('address')
                    ->label('Address')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
            ]);
    }
}

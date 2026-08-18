<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CookieSettings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';
    protected static string | \UnitEnum | null $navigationGroup = 'Store Management';
    protected static ?string $title = 'Cookie Management';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->getSchema('form')->fill([
            'cookie_enabled' => Setting::get('cookie_enabled', false),
            'cookie_message' => Setting::get('cookie_message', 'We use cookies to improve your experience on our site.'),
            'cookie_button_text' => Setting::get('cookie_button_text', 'Accept All'),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('GDPR & Cookie Consent')
                    ->description('Manage your website cookie consent banner.')
                    ->schema([
                        Toggle::make('cookie_enabled')
                            ->label('Enable Cookie Banner')
                            ->helperText('Show a consent banner to visitors.'),
                        Textarea::make('cookie_message')
                            ->label('Banner Message')
                            ->required(),
                        TextInput::make('cookie_button_text')
                            ->label('Button Text')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->getSchema('form')->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected string $view = 'filament.pages.cookie-settings';
}

<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use App\Models\Setting;
use Filament\Notifications\Notification;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 9;
    protected string $view = 'filament.pages.manage-settings';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected static ?string $title = 'Store Customization';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'notification_email' => Setting::get('notification_email'),
            'smtp_host' => Setting::get('smtp_host'),
            'smtp_port' => Setting::get('smtp_port'),
            'smtp_user' => Setting::get('smtp_user'),
            'smtp_pass' => Setting::get('smtp_pass'),
            'whatsapp_token' => Setting::get('whatsapp_token'),
            'whatsapp_phone_id' => Setting::get('whatsapp_phone_id'),
            'enable_cod' => Setting::get('enable_cod', false),
            'enable_card' => Setting::get('enable_card', false),
            'store_name' => Setting::get('store_name', 'My Super POS'),
            'store_phone' => Setting::get('store_phone'),
            'store_email' => Setting::get('store_email'),
            'store_address' => Setting::get('store_address'),
            'store_website' => Setting::get('store_website'),
            'store_logo' => Setting::get('store_logo'),
            'receipt_footer' => Setting::get('receipt_footer', 'Thank you for shopping with us!'),
            'business_reg' => Setting::get('business_reg'),
            'currency' => Setting::get('currency', '$'),
            'tax_rate' => Setting::get('tax_rate', '5'),
            'timezone' => Setting::get('timezone', 'UTC'),
            'firebase_api_key' => Setting::get('firebase_api_key'),
            'firebase_auth_domain' => Setting::get('firebase_auth_domain'),
            'firebase_project_id' => Setting::get('firebase_project_id'),
            'firebase_storage_bucket' => Setting::get('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => Setting::get('firebase_messaging_sender_id'),
            'firebase_app_id' => Setting::get('firebase_app_id'),
            'firebase_server_key' => Setting::get('firebase_server_key'),
        ]);
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Store Identity')
                            ->schema([
                                FileUpload::make('store_logo')
                                    ->label('Store Logo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings')
                                    ->visibility('public'),
                                TextInput::make('store_name')
                                    ->label('Store Name')
                                    ->required(),
                                TextInput::make('store_phone')
                                    ->label('Store Phone'),
                                TextInput::make('store_email')
                                    ->label('Store Email')
                                    ->email(),
                                TextInput::make('store_website')
                                    ->label('Store Website')
                                    ->url(),
                                TextInput::make('business_reg')
                                    ->label('Business Registration No.'),
                                Textarea::make('store_address')
                                    ->label('Store Address')
                                    ->rows(3),
                                Textarea::make('receipt_footer')
                                    ->label('Receipt Footer Message')
                                    ->rows(3)
                                    ->helperText('This text will appear at the bottom of printed receipts.'),
                            ])->columns(2),
                        Tabs\Tab::make('Regional & Tax')
                            ->schema([
                                Select::make('currency')
                                    ->label('Currency')
                                    ->options([
                                        '$' => 'USD ($)',
                                        '€' => 'EUR (€)',
                                        '£' => 'GBP (£)',
                                        'Rs' => 'LKR (Rs)',
                                        '₹' => 'INR (₹)',
                                        '¥' => 'JPY (¥)',
                                        'د.إ' => 'AED (د.إ)',
                                    ])
                                    ->required()
                                    ->searchable(),
                                TextInput::make('tax_rate')
                                    ->label('Default Tax Rate (%)')
                                    ->helperText('Fallback tax rate if no default tax is set in the Taxes module.')
                                    ->numeric()
                                    ->required(),
                                Select::make('timezone')
                                    ->label('Store Timezone')
                                    ->options(array_merge(['auto' => 'Auto-Detect (based on your location)'], collect(\DateTimeZone::listIdentifiers())->mapWithKeys(fn ($tz) => [$tz => $tz])->toArray()))
                                    ->searchable()
                                    ->required()
                                    ->helperText('Select "Auto-Detect" to automatically use your device\'s local time based on your IP address region.'),
                            ]),
                        Tabs\Tab::make('Notifications & SMTP')
                            ->schema([
                                TextInput::make('notification_email')
                                    ->label('Admin Notification Email')
                                    ->email(),
                                TextInput::make('smtp_host')->label('SMTP Host'),
                                TextInput::make('smtp_port')->label('SMTP Port'),
                                TextInput::make('smtp_user')->label('SMTP Username'),
                                TextInput::make('smtp_pass')->label('SMTP Password')->password(),
                            ]),
                        Tabs\Tab::make('WhatsApp API')
                            ->schema([
                                TextInput::make('whatsapp_token')->label('Access Token')->password(),
                                TextInput::make('whatsapp_phone_id')->label('Phone Number ID'),
                            ]),
                        Tabs\Tab::make('Payment Gateways')
                            ->schema([
                                Toggle::make('enable_cod')->label('Enable Cash on Delivery (COD)'),
                                Toggle::make('enable_card')->label('Enable Card Payments'),
                            ]),
                        Tabs\Tab::make('Firebase Notifications')
                            ->schema([
                                TextInput::make('firebase_api_key')->label('API Key'),
                                TextInput::make('firebase_auth_domain')->label('Auth Domain'),
                                TextInput::make('firebase_project_id')->label('Project ID'),
                                TextInput::make('firebase_storage_bucket')->label('Storage Bucket'),
                                TextInput::make('firebase_messaging_sender_id')->label('Messaging Sender ID'),
                                TextInput::make('firebase_app_id')->label('App ID'),
                                TextInput::make('firebase_server_key')
                                    ->label('Server Key (FCM)')
                                    ->helperText('Used for pushing notifications from the backend.')
                                    ->password(),
                            ])->columns(2),
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($key === 'store_logo' && is_array($value)) {
                $value = collect($value)->first();
            }
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings Saved Successfully')
            ->success()
            ->send();
    }
}

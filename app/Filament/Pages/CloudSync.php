<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Lend;
use App\Models\Order;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class CloudSync extends Page
{
    // Properties removed to prevent type signature mismatch with Filament versions
    protected static ?int $navigationSort = 100;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected string $view = 'filament.pages.cloud-sync';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pullProducts')
                ->label('Pull Products from Cloud')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->requiresConfirmation()
                ->action(fn () => $this->syncPull()),

            Action::make('pushOrders')
                ->label('Push Local Sales to Cloud')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn () => $this->syncPush()),
        ];
    }

    public function syncPull()
    {
        $url = env('SYNC_SERVER_URL').'/api/sync/products';
        $token = env('SYNC_API_TOKEN');

        if (! $url || ! $token) {
            Notification::make()->title('Sync URL or Token not configured in .env')->danger()->send();

            return;
        }

        try {
            $response = Http::withToken($token)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                DB::beginTransaction();

                Schema::disableForeignKeyConstraints();
                // Clear local products and categories
                Product::truncate();
                Category::truncate();
                Schema::enableForeignKeyConstraints();

                // Insert Categories
                foreach ($data['categories'] ?? [] as $category) {
                    Category::insert($category);
                }

                // Insert Products
                foreach ($data['products'] ?? [] as $product) {
                    Product::insert($product);
                }

                DB::commit();

                Notification::make()->title('Products Synced Successfully!')->success()->send();
            } else {
                Notification::make()->title('Sync Failed')->body($response->body())->danger()->send();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Connection Error')->body($e->getMessage())->danger()->send();
        }
    }

    public function syncPush()
    {
        $url = env('SYNC_SERVER_URL').'/api/sync/orders';
        $token = env('SYNC_API_TOKEN');

        if (! $url || ! $token) {
            Notification::make()->title('Sync URL or Token not configured in .env')->danger()->send();

            return;
        }

        // Get unsynced orders and lends
        $orders = Order::with('items')->whereNull('synced_at')->get();
        $lends = Lend::whereNull('synced_at')->get();

        if ($orders->isEmpty() && $lends->isEmpty()) {
            Notification::make()->title('Everything is up to date!')->info()->send();

            return;
        }

        try {
            $response = Http::withToken($token)->post($url, [
                'orders' => $orders->toArray(),
                'lends' => $lends->toArray(),
            ]);

            $orderIds = $orders->pluck('id');
            $lendIds = $lends->pluck('id');

            if ($response->successful()) {
                // Mark as synced locally
                if ($orderIds->isNotEmpty()) {
                    Order::whereIn('id', $orderIds)->update(['synced_at' => now()]);
                }
                if ($lendIds->isNotEmpty()) {
                    Lend::whereIn('id', $lendIds)->update(['synced_at' => now()]);
                }

                Notification::make()->title('Sales Pushed Successfully!')->success()->send();
            } else {
                Notification::make()->title('Push Failed')->body($response->body())->danger()->send();
            }
        } catch (\Exception $e) {
            Notification::make()->title('Connection Error')->body($e->getMessage())->danger()->send();
        }
    }
}

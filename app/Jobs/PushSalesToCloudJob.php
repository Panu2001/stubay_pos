<?php

namespace App\Jobs;

use App\Models\Lend;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PushSalesToCloudJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function handle(): void
    {
        $url = env('SYNC_SERVER_URL').'/api/sync/orders';
        $token = env('SYNC_API_TOKEN');

        if (! $url || ! $token) {
            return;
        }

        $orders = Order::with('items')->whereNull('synced_at')->get();
        $lends = Lend::whereNull('synced_at')->get();

        if ($orders->isEmpty() && $lends->isEmpty()) {
            return;
        }

        $response = Http::withToken($token)
            ->timeout(10)
            ->post($url, [
                'orders' => $orders->toArray(),
                'lends' => $lends->toArray(),
            ]);

        $orderIds = $orders->pluck('id');
        $lendIds = $lends->pluck('id');

        if ($response->successful()) {
            if ($orderIds->isNotEmpty()) {
                Order::whereIn('id', $orderIds)->update(['synced_at' => now()]);
            }
            if ($lendIds->isNotEmpty()) {
                Lend::whereIn('id', $lendIds)->update(['synced_at' => now()]);
            }
        } else {
            $this->release(60); // Retry in 60 seconds if it failed
        }
    }
}

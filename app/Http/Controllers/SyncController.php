<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Lend;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * Called by the local app to get products from the online server.
     */
    public function pullProducts(Request $request)
    {
        if ($request->header('Authorization') !== 'Bearer '.env('SYNC_API_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'categories' => Category::all(),
            'products' => Product::all(),
        ]);
    }

    /**
     * Called by the local app to push local orders to the online server.
     */
    public function pushOrders(Request $request)
    {
        if ($request->header('Authorization') !== 'Bearer '.env('SYNC_API_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $orders = $request->input('orders', []);
        $lends = $request->input('lends', []);

        DB::beginTransaction();
        try {
            $orderIdMap = [];

            // Re-create orders on the server
            foreach ($orders as $localOrder) {
                $orderItems = $localOrder['items'] ?? [];
                $localOrderId = $localOrder['id'] ?? null;
                unset($localOrder['id']); // Let server generate new ID
                unset($localOrder['items']);

                $serverOrder = Order::updateOrCreate(
                    ['order_number' => $localOrder['order_number']],
                    $localOrder
                );

                if ($localOrderId) {
                    $orderIdMap[$localOrderId] = $serverOrder->id;
                }

                // Re-create items
                $serverOrder->items()->delete();
                foreach ($orderItems as $item) {
                    unset($item['id']);
                    unset($item['order_id']); // Let server assign
                    $serverOrder->items()->create($item);
                }
            }

            // Re-create lends on the server
            foreach ($lends as $localLend) {
                unset($localLend['id']);

                if (! empty($localLend['order_id']) && isset($orderIdMap[$localLend['order_id']])) {
                    $localLend['order_id'] = $orderIdMap[$localLend['order_id']];
                }

                Lend::updateOrCreate([
                    'customer_id' => $localLend['customer_id'],
                    'created_at' => $localLend['created_at'],
                ], $localLend);
            }

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

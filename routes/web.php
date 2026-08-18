<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;

Route::get('/product/print/{product}', [\App\Http\Controllers\ProductPrintController::class, 'show'])->name('product.print')->middleware('auth');

Route::get('/customer/print-lends/{customer}', [\App\Http\Controllers\CustomerPrintLendsController::class, 'show'])
    ->name('customer.print-lends')
    ->middleware('auth');

Route::get('/shop/print-lends', [\App\Http\Controllers\ShopPrintLendsController::class, 'show'])
    ->name('shop.print-lends')
    ->middleware('auth');

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/receipt/{order}', function (Order $order) {
    return view('receipt', compact('order'));
})->name('receipt')->middleware('web');


Route::get('/cash-entry-receipt/{entry}', function (\App\Models\CashEntry $entry) {
    return view('cash-entry-receipt', compact('entry'));
})->name('cash-entry-receipt')->middleware('web');

Route::get('/supplier-purchase-receipt/{purchase}', function (\App\Models\SupplierPurchase $purchase) {
    return view('supplier-purchase-receipt', compact('purchase'));
})->name('supplier-purchase.receipt')->middleware('web');

Route::get('/export-csv', [App\Http\Controllers\ExportController::class, 'csv'])->name('admin.export.csv');



// Fallback route to serve images if the storage symlink is broken
Route::get('/storage/{path}', function ($path) {
    $path = filter_var($path, FILTER_SANITIZE_URL);
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath) || is_dir($fullPath)) {
        abort(404);
    }

    $mimeType = Illuminate\Support\Facades\File::mimeType($fullPath);
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

// Customer Display for Dual Monitor setups
Route::get('/customer-display', function () {
    return view('pos.customer-display');
})->name('pos.customer-display')->middleware(['auth', 'web']);





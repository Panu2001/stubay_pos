<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$token = '72941-fix-price-history';

if (($_GET['token'] ?? '') !== $token) {
    http_response_code(403);
    exit('Forbidden');
}

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$messages = [];

try {
    $connection = config('database.default');
    $database = config("database.connections.{$connection}.database");
    $basePath = base_path();

    $messages[] = "Database connection: {$connection}";
    $messages[] = "Database file: {$database}";

    if (! Schema::hasTable('product_price_histories')) {
        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('old_cost_price', 10, 2)->nullable();
            $table->decimal('new_cost_price', 10, 2)->default(0);
            $table->decimal('old_sell_price', 10, 2)->nullable();
            $table->decimal('new_sell_price', 10, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        $messages[] = 'Created missing table: product_price_histories';
    } else {
        $messages[] = 'Table exists: product_price_histories';
    }

    Artisan::call('optimize:clear');
    $messages[] = 'Cleared Laravel cache/view cache.';

    if (function_exists('opcache_reset')) {
        opcache_reset();
        $messages[] = 'Reset PHP opcache.';
    } else {
        $messages[] = 'PHP opcache reset function is not available.';
    }

    $fileChecks = [
        'ProductForm.php has Price History section' => [
            'path' => "{$basePath}/app/Filament/Resources/Products/Schemas/ProductForm.php",
            'needle' => "Section::make('Price History')",
        ],
        'ProductForm.php has history preview helper' => [
            'path' => "{$basePath}/app/Filament/Resources/Products/Schemas/ProductForm.php",
            'needle' => 'priceHistoryPreview',
        ],
        'EditProduct.php has direct priceHistories tab' => [
            'path' => "{$basePath}/app/Filament/Resources/Products/Pages/EditProduct.php",
            'needle' => "'priceHistories' => Tab::make('Price Histories')",
        ],
        'Relation manager fallback file exists' => [
            'path' => "{$basePath}/resources/views/vendor/filament-panels/resources/relation-manager.blade.php",
            'needle' => 'PriceHistoriesRelationManager',
        ],
        'Price history Blade file exists' => [
            'path' => "{$basePath}/resources/views/filament/resources/products/relation-managers/price-histories.blade.php",
            'needle' => 'pos-price-history-table',
        ],
        'Product model saves history' => [
            'path' => "{$basePath}/app/Models/Product.php",
            'needle' => 'ProductPriceHistory::create',
        ],
    ];

    foreach ($fileChecks as $label => $check) {
        $exists = is_file($check['path']);
        $contents = $exists ? file_get_contents($check['path']) : false;
        $status = $contents !== false && str_contains($contents, $check['needle']) ? 'OK' : 'MISSING';
        $modified = $exists ? date('Y-m-d H:i:s', filemtime($check['path'])) : 'file not found';
        $hash = $contents !== false ? substr(sha1($contents), 0, 12) : 'no hash';

        $messages[] = "{$status}: {$label} | {$check['path']} | modified {$modified} | hash {$hash}";
    }

    $count = DB::table('product_price_histories')->count();
    $messages[] = "History rows currently saved: {$count}";

    $latest = DB::table('product_price_histories')
        ->orderByDesc('id')
        ->limit(5)
        ->get();
} catch (Throwable $exception) {
    http_response_code(500);
    echo '<h1>Repair failed</h1>';
    echo '<pre>' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Price History Repair</title>
    <style>
        body { background: #111; color: #eee; font-family: Arial, sans-serif; padding: 32px; }
        .box { max-width: 960px; margin: auto; background: #1b1b1f; border: 1px solid #333; border-radius: 10px; padding: 24px; }
        li { margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #333; padding: 10px; text-align: left; }
        th { color: #aaa; }
        .ok { color: #00d39b; font-weight: 700; }
    </style>
</head>
<body>
    <div class="box">
        <h1 class="ok">Price history repair completed</h1>
        <ul>
            <?php foreach ($messages as $message): ?>
                <li><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>

        <h2>Latest history rows</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product ID</th>
                    <th>Old Price</th>
                    <th>New Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latest as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $row->id, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $row->product_id, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $row->old_sell_price, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $row->new_sell_price, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $row->created_at, ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($latest->isEmpty()): ?>
                    <tr><td colspan="5">No history rows yet. Change a product price after this repair, then reload this page.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

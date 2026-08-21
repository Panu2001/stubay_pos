<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pending = \App\Models\SupplierPurchase::where('owed_amount', '>', 0)->get();
$all = \App\Models\SupplierPurchase::all();

$output = [
    'pending_count' => $pending->count(),
    'all_count' => $all->count(),
    'pending_total' => $pending->sum('total_amount'),
    'pending_paid' => $pending->sum('paid_amount'),
    'pending_owed' => $pending->sum('owed_amount'),
    'all_total' => $all->sum('total_amount'),
    'all_paid' => $all->sum('paid_amount'),
    'all_owed' => $all->sum('owed_amount'),
    'raw_data' => $all->toArray()
];

file_put_contents('scratch_output.json', json_encode($output, JSON_PRETTY_PRINT));
echo "Done";

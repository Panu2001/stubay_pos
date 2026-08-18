<?php
$file = 'resources/views/filament/pages/pos-terminal.blade.php';
$content = file_get_contents($file);

$replacements = [
    "->select(['id', 'name', 'barcode', 'price', 'unit', 'stock_quantity'])" => "->select(['id', 'name', 'barcode', 'price', 'cost_price', 'unit', 'stock_quantity'])",
    
    "'price' => (float) \$product->price,\r\n                'unit' => \$product->unit," => "'price' => (float) \$product->price,\r\n                'cost_price' => (float) \$product->cost_price,\r\n                'unit' => \$product->unit,",

    "'price' => (float) (\$batch->price ?? \$product->price),\r\n                    'unit' => \$product->unit," => "'price' => (float) (\$batch->price ?? \$product->price),\r\n                    'cost_price' => (float) (\$batch->cost_price ?? \$product->cost_price),\r\n                    'unit' => \$product->unit,",

    "price: {{ \$product->price }}, unit: '{{ \$product->unit }}'" => "price: {{ \$product->price }}, cost_price: {{ \$product->cost_price ?? 0 }}, unit: '{{ \$product->unit }}'",

    "price: {{ \$batch['price'] }}, unit: '{{ \$product->unit }}'" => "price: {{ \$batch['price'] }}, cost_price: {{ \$batch['cost_price'] ?? 0 }}, unit: '{{ \$product->unit }}'",
];

foreach ($replacements as $search => $replace) {
    if (strpos($content, $search) === false) {
        echo "Failed to find: " . substr($search, 0, 50) . "\n";
    }
    $content = str_replace($search, $replace, $content);
}

file_put_contents($file, $content);
echo "Done.";

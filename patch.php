<?php
$file = 'resources/views/filament/pages/pos-terminal.blade.php';
$content = file_get_contents($file);

$search = "addToCart(product) {\r\n                const productId";
$replace = "addToCart(product) {\r\n                if (!product.cost_price || parseFloat(product.cost_price) <= 0) {\r\n                    this.costPriceWarnings = this.costPriceWarnings || {};\r\n                    let warnings = this.costPriceWarnings[product.id] || 0;\r\n                    if (warnings < 3) {\r\n                        this.costPriceWarnings[product.id] = warnings + 1;\r\n                        this.\$wire.posAlert('Suggestion: Please add a cost price for ' + product.name + ' via Price Adjustments! (Warning ' + (warnings + 1) + ' of 3)');\r\n                    } else {\r\n                        this.\$wire.posAlert('BLOCKED: You cannot add ' + product.name + ' without a cost price. Please add it via Price Adjustments.');\r\n                        return;\r\n                    }\r\n                }\r\n\r\n                const productId";

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done replacing JS.";

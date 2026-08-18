<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductPrintController extends Controller
{
    public function show(Request $request, Product $product)
    {
        $generator = new BarcodeGeneratorPNG();
        // Fallback to empty string if barcode is null
        $barcodeValue = $product->barcode ?? '00000000';
        $barcode = base64_encode($generator->getBarcode($barcodeValue, $generator::TYPE_CODE_128));

        $width = $request->query('width', 40);
        $height = $request->query('height', 25);
        $copies = $request->query('copies', 1);
        $columns = $request->query('columns', 2);
        $horizontal_gap = $request->query('horizontal_gap', 2.5);
        $vertical_gap = $request->query('vertical_gap', 3);

        return view('products.print', compact('product', 'barcode', 'width', 'height', 'copies', 'columns', 'horizontal_gap', 'vertical_gap'));
    }
}

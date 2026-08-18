<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    public function csv(Request $request)
    {
        $type = $request->query('type', 'sales');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $format = $request->query('format', 'csv');

        if ($format === 'print') {
            return $this->handlePrint($type, $startDate, $endDate);
        }

        if ($type === 'inventory') {
            return $this->exportInventory();
        }

        return $this->exportSales($startDate, $endDate);
    }

    protected function handlePrint($type, $startDate, $endDate)
    {
        if ($type === 'inventory') {
            $data = $this->getInventoryData();
            return view('reports.print', [
                'title' => 'Inventory Status Report',
                'startDate' => null,
                'endDate' => null,
                'headers' => ['Product Name', 'Category', 'Price', 'Cost Price', 'Stock', 'Value'],
                'rows' => $data['rows'],
                'summary' => ['Total Inventory Value' => 'Rs. ' . number_format($data['total_value'], 2)],
            ]);
        }

        $data = $this->getSalesData($startDate, $endDate);
        return view('reports.print', [
            'title' => 'Sales Detail Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'headers' => ['Date', 'Order #', 'Customer', 'Payment', 'Cost', 'Revenue', 'Profit'],
            'rows' => $data['rows'],
            'summary' => [
                'Total Orders' => $data['total_orders'],
                'Total Cost' => 'Rs. ' . number_format($data['total_cost'], 2),
                'Total Revenue' => 'Rs. ' . number_format($data['total_revenue'], 2),
                'Total Profit' => 'Rs. ' . number_format($data['total_profit'], 2)
            ],
        ]);
    }

    protected function getSalesData($startDate, $endDate)
    {
        $orders = Order::query()
            ->when($startDate, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($endDate, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->with('customer')
            ->get();
    
        $rows = [];
        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;
        foreach ($orders as $order) {
            $rows[] = [
                $order->created_at->format('Y-m-d'),
                $order->order_number,
                $order->customer?->name ?? 'N/A',
                $order->payment_method,
                'Rs. ' . number_format($order->total_cost, 2),
                'Rs. ' . number_format($order->total, 2),
                'Rs. ' . number_format($order->total_profit, 2)
            ];
            $totalRevenue += $order->total;
            $totalCost += $order->total_cost;
            $totalProfit += $order->total_profit;
        }
    
        return [
            'rows' => $rows,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'total_orders' => $orders->count(),
        ];
    }

    protected function getInventoryData()
    {
        $products = Product::with('category')->get();
        $rows = [];
        $totalValue = 0;
        foreach ($products as $product) {
            $value = $product->stock_quantity * $product->cost_price;
            $rows[] = [
                $product->name,
                $product->category?->name ?? 'N/A',
                'Rs. ' . number_format($product->price, 2),
                'Rs. ' . number_format($product->cost_price, 2),
                $product->stock_quantity,
                'Rs. ' . number_format($value, 2)
            ];
            $totalValue += $value;
        }

        return [
            'rows' => $rows,
            'total_value' => $totalValue,
        ];
    }

    protected function exportSales($startDate, $endDate)
    {
        $orders = Order::query()
            ->when($startDate, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($endDate, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->with('customer')
            ->get();
    
        $output = "Date,Order #,Customer,Payment Method,Cost,Total Revenue,Profit\n";
    
        foreach ($orders as $order) {
            $row = [
                $order->created_at->format('Y-m-d H:i:s'),
                $order->order_number,
                $order->customer?->name ?? 'Walk-in Customer',
                $order->payment_method,
                $order->total_cost,
                $order->total,
                $order->total_profit,
            ];
            
            // Basic CSV escaping
            $row = array_map(function($val) {
                return '"' . str_replace('"', '""', $val) . '"';
            }, $row);
            
            $output .= implode(',', $row) . "\n";
        }
    
        return Response::make($output, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report-' . now()->format('Y-m-d') . '.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    protected function exportInventory()
    {
        $products = Product::with('category')->get();

        $output = "Product Name,Category,Price,Cost Price,Current Stock,Stock Value (Cost)\n";

        foreach ($products as $product) {
            $row = [
                $product->name,
                $product->category?->name ?? 'N/A',
                $product->price,
                $product->cost_price,
                $product->stock_quantity,
                $product->stock_quantity * $product->cost_price,
            ];
            
            // Basic CSV escaping
            $row = array_map(function($val) {
                return '"' . str_replace('"', '""', $val) . '"';
            }, $row);
            
            $output .= implode(',', $row) . "\n";
        }

        return Response::make($output, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-report-' . now()->format('Y-m-d') . '.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}

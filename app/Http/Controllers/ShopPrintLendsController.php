<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class ShopPrintLendsController extends Controller
{
    public function show(Request $request)
    {
        // Get all customers who have lends
        $customers = Customer::whereHas('lends')->with('lends')->get();

        $rows = [];
        $totalShopBorrowed = 0;
        $totalShopPaid = 0;
        $totalShopOutstanding = 0;

        foreach ($customers as $customer) {
            $totalBorrowed = $customer->lends->sum('total_amount');
            $totalPaid = $customer->lends->sum('paid_amount');
            $outstanding = $customer->lends->sum(fn ($lend) => $lend->remaining_amount);

            $rows[] = [
                'customer' => $customer,
                'total_borrowed' => $totalBorrowed,
                'total_paid' => $totalPaid,
                'outstanding' => $outstanding,
            ];

            $totalShopBorrowed += $totalBorrowed;
            $totalShopPaid += $totalPaid;
            $totalShopOutstanding += $outstanding;
        }

        // Sort by outstanding balance descending to show largest balance first
        usort($rows, fn($a, $b) => $b['outstanding'] <=> $a['outstanding']);

        return view('lends.shop_print', compact(
            'rows',
            'totalShopBorrowed',
            'totalShopPaid',
            'totalShopOutstanding'
        ));
    }
}

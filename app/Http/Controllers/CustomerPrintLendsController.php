<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerPrintLendsController extends Controller
{
    public function show(Request $request, Customer $customer)
    {
        // Get all lends of this customer
        $lends = $customer->lends()->with(['order.items.product'])->orderBy('created_at', 'desc')->get();

        // Calculate summary statistics
        $totalLends = $lends->count();
        $totalAmount = $lends->sum('total_amount');
        $totalPaid = $lends->sum('paid_amount');
        $totalOutstanding = $lends->sum(fn ($lend) => $lend->remaining_amount);

        return view('lends.print', compact(
            'customer',
            'lends',
            'totalLends',
            'totalAmount',
            'totalPaid',
            'totalOutstanding'
        ));
    }
}

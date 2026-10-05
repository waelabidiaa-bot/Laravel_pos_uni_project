<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $paidToday = Sale::where('status', 'PAID')->whereDate('sale_date', today());

        $stats = [
            'sales_today'   => (clone $paidToday)->count(),
            'revenue_today' => (clone $paidToday)->sum('total'),
            'low_stock'     => Stock::whereColumn('quantity', '<=', 'min_threshold')->count(),
            'cashiers'      => User::where('role', 'CASHIER')->count(),
        ];

        $latestSales = Sale::with('cashSession.cashier')->orderByDesc('sale_date')->take(10)->get();

        return view('admin.dashboard', compact('stats', 'latestSales'));
    }
}

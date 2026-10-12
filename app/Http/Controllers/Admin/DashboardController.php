<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's paid sales
        $paidToday = Sale::where('status', 'PAID')
            ->whereDate('sale_date', today());

        // Dashboard KPI statistics
        $stats = [
            'sales_today' => (clone $paidToday)->count(),

            'revenue_today' => (clone $paidToday)->sum('total'),

            'low_stock' => Stock::whereColumn(
                'quantity',
                '<=',
                'min_threshold'
            )->count(),

            'cashiers' => User::where('role', 'CASHIER')->count(),
        ];

        // Latest 10 sales, including their cashier information
        $latestSales = Sale::with('cashSession.cashier')
            ->orderByDesc('sale_date')
            ->take(10)
            ->get();

        // Products that have reached their minimum stock threshold
        $lowStockProducts = Stock::with('product')
            ->whereColumn('quantity', '<=', 'min_threshold')
            ->orderBy('quantity')
            ->take(5)
            ->get();

        // Prepare the last seven days for the sales chart
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today()->endOfDay();

        $dailySales = Sale::where('status', 'PAID')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->selectRaw('DATE(sale_date) as day')
            ->selectRaw('COUNT(*) as sales_count')
            ->selectRaw('COALESCE(SUM(total), 0) as revenue')
            ->groupByRaw('DATE(sale_date)')
            ->get()
            ->keyBy('day');

        $chartLabels = [];
        $chartRevenue = [];
        $chartSales = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->subDays(6 - $i);
            $dayKey = $date->toDateString();

            $chartLabels[] = $date->format('D, d M');

            $chartRevenue[] = (float) ($dailySales->get($dayKey)?->revenue ?? 0);

            $chartSales[] = (int) ($dailySales->get($dayKey)?->sales_count ?? 0);
        }

        $salesChart = [
            'labels' => $chartLabels,
            'revenue' => $chartRevenue,
            'sales' => $chartSales,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'latestSales',
            'lowStockProducts',
            'salesChart'
        ));
    }
}
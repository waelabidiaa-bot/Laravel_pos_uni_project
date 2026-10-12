<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        // Validate optional date filters.
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        // Only fully paid sales count toward financial statistics.
        $salesQuery = $this->paidSalesQuery($from, $to);

        $totalRevenue = (float) (clone $salesQuery)->sum('total');
        $totalSales = (clone $salesQuery)->count();

        $averageSale = $totalSales > 0
            ? $totalRevenue / $totalSales
            : 0;

        // Count items belonging to paid sales in the selected period.
        $productsSold = SaleItem::whereHas('sale', function ($query) use ($from, $to) {
            $this->applyPaidSaleFilters($query, $from, $to);
        })->sum('quantity');

        // Estimate cost using each product's CURRENT purchase price.
        $totalCost = (float) DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($query) =>
                $query->whereDate('sales.sale_date', '>=', $from)
            )
            ->when($to, fn ($query) =>
                $query->whereDate('sales.sale_date', '<=', $to)
            )
            ->selectRaw(
                'COALESCE(
                    SUM(
                        sale_items.quantity *
                        COALESCE(
                            sale_items.purchase_price_at_sale,
                            products.purchase_price,
                            0
                        )
                    ),
                    0
                ) AS total_cost'
            )
            ->value('total_cost');

        $grossProfit = $totalRevenue - $totalCost;

        $profitMargin = $totalRevenue > 0
            ? ($grossProfit / $totalRevenue) * 100
            : 0;

        // Find the five most frequently sold products.
        // Daily revenue from completed sales.
$dailyRevenue = DB::table('sales')
    ->where('status', 'PAID')
    ->when($from, fn ($query) =>
        $query->whereDate('sale_date', '>=', $from)
    )
    ->when($to, fn ($query) =>
        $query->whereDate('sale_date', '<=', $to)
    )
    ->selectRaw('DATE(sale_date) AS date, SUM(total) AS revenue')
    ->groupByRaw('DATE(sale_date)')
    ->get()
    ->keyBy('date');

// Daily product costs, using historical purchase prices when available.
$dailyCosts = DB::table('sale_items')
    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
    ->join('products', 'products.id', '=', 'sale_items.product_id')
    ->where('sales.status', 'PAID')
    ->when($from, fn ($query) =>
        $query->whereDate('sales.sale_date', '>=', $from)
    )
    ->when($to, fn ($query) =>
        $query->whereDate('sales.sale_date', '<=', $to)
    )
    ->selectRaw('
        DATE(sales.sale_date) AS date,
        SUM(
            sale_items.quantity *
            COALESCE(
                sale_items.purchase_price_at_sale,
                products.purchase_price,
                0
            )
        ) AS cost
    ')
    ->groupByRaw('DATE(sales.sale_date)')
    ->get()
    ->keyBy('date');

// Combine daily revenue and costs into one dataset.
$dates = $dailyRevenue->keys()
    ->merge($dailyCosts->keys())
    ->unique()
    ->sort()
    ->values();

$dailyPerformance = $dates->map(function ($date) use (
    $dailyRevenue,
    $dailyCosts
) {
    $revenue = (float) ($dailyRevenue->get($date)->revenue ?? 0);
    $cost = (float) ($dailyCosts->get($date)->cost ?? 0);

    return [
        'date' => $date,
        'revenue' => round($revenue, 3),
        'profit' => round($revenue - $cost, 3),
    ];
});
        // Top five best-selling products from paid sales.
        $topSellingProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($query) =>
                $query->whereDate('sales.sale_date', '>=', $from)
            )
            ->when($to, fn ($query) =>
                $query->whereDate('sales.sale_date', '<=', $to)
            )
            ->select(
                'products.id',
                'products.name',
                'products.image'
            )
            ->selectRaw('SUM(sale_items.quantity) AS quantity_sold')
            ->selectRaw('SUM(sale_items.quantity * sale_items.unit_price) AS revenue')
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();
        return view('admin.statistics.index', compact(
            'totalRevenue',
            'totalSales',
            'productsSold',
            'averageSale',
            'totalCost',
            'grossProfit',
            'profitMargin',
            'topSellingProducts',
            'from',
            'to',
            'dailyPerformance'
));
    }

    /**
     * Query paid sales, optionally filtered by date.
     */
    private function paidSalesQuery(?string $from, ?string $to): Builder
    {
        $query = Sale::query();

        $this->applyPaidSaleFilters($query, $from, $to);

        return $query;
    }

    /**
     * Apply the same conditions wherever paid sales are counted.
     */
    private function applyPaidSaleFilters(
        Builder $query,
        ?string $from,
        ?string $to
    ): Builder {
        return $query
            ->where('status', 'PAID')
            ->when($from, fn ($q) =>
                $q->whereDate('sale_date', '>=', $from)
            )
            ->when($to, fn ($q) =>
                $q->whereDate('sale_date', '<=', $to)
            );
    }
}
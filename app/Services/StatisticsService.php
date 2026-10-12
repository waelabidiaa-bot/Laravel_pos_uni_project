<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsService
{
    /**
     * Return financial statistics for a selected period.
     *
     * Only PAID sales are included.
     */
    public function getSummary(
        ?string $from = null,
        ?string $to = null
    ): array {
        // Default to the current month when no dates are supplied.
        if ($from === null && $to === null) {
            $from = Carbon::now()->startOfMonth()->toDateString();
            $to = Carbon::now()->toDateString();
        }

        // Calculate revenue and number of paid sales.
        $salesQuery = DB::table('sales')
            ->where('status', 'PAID')
            ->when($from, fn ($query) =>
                $query->whereDate('sale_date', '>=', $from)
            )
            ->when($to, fn ($query) =>
                $query->whereDate('sale_date', '<=', $to)
            );

        $totalRevenue = (float) (clone $salesQuery)->sum('total');
        $totalSales = (clone $salesQuery)->count();

        // Calculate the total quantity of products sold.
        $productsSold = (int) DB::table('sale_items')
            ->join(
                'sales',
                'sales.id',
                '=',
                'sale_items.sale_id'
            )
            ->where('sales.status', 'PAID')
            ->when($from, fn ($query) =>
                $query->whereDate('sales.sale_date', '>=', $from)
            )
            ->when($to, fn ($query) =>
                $query->whereDate('sales.sale_date', '<=', $to)
            )
            ->sum('sale_items.quantity');

        // Calculate product costs using historical purchase prices.
        // Fall back to the current purchase price when historical
        // data is unavailable.
        $totalCost = (float) DB::table('sale_items')
            ->join(
                'sales',
                'sales.id',
                '=',
                'sale_items.sale_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'sale_items.product_id'
            )
            ->where('sales.status', 'PAID')
            ->when($from, fn ($query) =>
                $query->whereDate('sales.sale_date', '>=', $from)
            )
            ->when($to, fn ($query) =>
                $query->whereDate('sales.sale_date', '<=', $to)
            )
            ->selectRaw('
                COALESCE(
                    SUM(
                        sale_items.quantity *
                        COALESCE(
                            sale_items.purchase_price_at_sale,
                            products.purchase_price,
                            0
                        )
                    ),
                    0
                ) AS total_cost
            ')
            ->value('total_cost');

        $grossProfit = $totalRevenue - $totalCost;

        $profitMargin = $totalRevenue > 0
            ? ($grossProfit / $totalRevenue) * 100
            : 0;

        $averageSale = $totalSales > 0
            ? $totalRevenue / $totalSales
            : 0;

        // Rank products by the gross profit they generated.
        $topProfitableProducts = DB::table('sale_items')
            ->join(
                'sales',
                'sales.id',
                '=',
                'sale_items.sale_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'sale_items.product_id'
            )
            ->where('sales.status', 'PAID')
            ->when($from, fn ($query) =>
                $query->whereDate('sales.sale_date', '>=', $from)
            )
            ->when($to, fn ($query) =>
                $query->whereDate('sales.sale_date', '<=', $to)
            )
            ->select(
                'products.id',
                'products.name'
            )
            ->selectRaw('
                SUM(sale_items.quantity) AS quantity_sold
            ')
            ->selectRaw('
                SUM(
                    sale_items.quantity * sale_items.unit_price
                ) AS revenue
            ')
            ->selectRaw('
                SUM(
                    sale_items.quantity *
                    COALESCE(
                        sale_items.purchase_price_at_sale,
                        products.purchase_price,
                        0
                    )
                ) AS cost
            ')
            ->selectRaw('
                SUM(
                    sale_items.quantity *
                    (
                        sale_items.unit_price -
                        COALESCE(
                            sale_items.purchase_price_at_sale,
                            products.purchase_price,
                            0
                        )
                    )
                ) AS gross_profit
            ')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('gross_profit')
            ->limit(5)
            ->get()
            ->map(fn ($product) => [
                'name' => $product->name,
                'quantity_sold' => (int) $product->quantity_sold,
                'revenue' => round((float) $product->revenue, 3),
                'cost' => round((float) $product->cost, 3),
                'gross_profit' => round(
                    (float) $product->gross_profit,
                    3
                ),
            ])
            ->values()
            ->all();

        return [
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
            'currency' => $currency,
            'total_revenue' => round($totalRevenue, 3),
            'total_cost' => round($totalCost, 3),
            'gross_profit' => round($grossProfit, 3),
            'profit_margin_percent' => round($profitMargin, 2),
            'total_sales' => $totalSales,
            'products_sold' => $productsSold,
            'average_sale' => round($averageSale, 3),
            'top_profitable_products' => $topProfitableProducts,
        ];
    }
}
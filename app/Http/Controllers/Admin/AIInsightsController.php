<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIInsightsController extends Controller
{
    /**
     * Display AI insights based on real POS data.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'keep_alive' => '30m',
        ]);

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        // Only fully paid sales count toward financial statistics.
        $sales = DB::table('sales')
            ->where('status', 'PAID')
            ->when($from, fn ($q) => $q->whereDate('sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sale_date', '<=', $to));

        $totalSales = (clone $sales)->count();
        $revenue = (float) (clone $sales)->sum('total');

        $cost = (float) DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($q) => $q->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sales.sale_date', '<=', $to))
            ->selectRaw('COALESCE(SUM(
                sale_items.quantity * COALESCE(
                    sale_items.purchase_price_at_sale,
                    products.purchase_price,
                    0
                )
            ), 0) AS total_cost')
            ->value('total_cost');

        $grossProfit = $revenue - $cost;
        $profitMargin = $revenue > 0
            ? ($grossProfit / $revenue) * 100
            : 0;

        $productsSold = (int) DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($q) => $q->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sales.sale_date', '<=', $to))
            ->sum('sale_items.quantity');

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($q) => $q->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sales.sale_date', '<=', $to))
            ->select('products.name')
            ->selectRaw('SUM(sale_items.quantity) AS quantity_sold')
            ->selectRaw('SUM(sale_items.quantity * sale_items.unit_price) AS revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();

        // Stock is current inventory, so it is not restricted by the sales dates.
        $lowStock = DB::table('stock')
            ->join('products', 'products.id', '=', 'stock.product_id')
            ->whereColumn('stock.quantity', '<=', 'stock.min_threshold')
            ->select(
                'products.name',
                'stock.quantity',
                'stock.min_threshold'
            )
            ->orderBy('stock.quantity')
            ->limit(20)
            ->get();

        $currency = Parametre::first()?->currency ?? 'TND';

        $data = [
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
            'currency' => $currency,
            'total_sales' => $totalSales,
            'revenue' => round($revenue, 3),
            'estimated_cost' => round($cost, 3),
            'gross_profit' => round($grossProfit, 3),
            'profit_margin_percent' => round($profitMargin, 2),
            'products_sold' => $productsSold,
            'top_products' => $topProducts,
            'low_stock_products' => $lowStock,
        ];

        $insights = null;
        $error = null;

        // Generate the overview only when the administrator requests it.
        if ($request->boolean('analyze')) {
            try {
                $insights = $this->askOllama(
                    'Analyze these POS statistics. Explain business performance, '
                    . 'important trends visible in the supplied data, and practical '
                    . 'actions the administrator could consider. Discuss low-stock '
                    . 'items and best sellers. Do not invent figures or claim a trend '
                    . 'when the data does not support it. Mention that estimated cost '
                    . 'uses recorded historical purchase prices when available, otherwise '
                    . 'the current purchase price. Keep the answer concise and readable.',
                    $data
                );
            } catch (\Throwable $exception) {
                Log::error('POS AI overview failed: ' . $exception->getMessage());
                $error = 'Could not contact the local AI. Check that Ollama is running.';
            }
        }

        return view('admin.ai-insights.index', compact(
            'data',
            'insights',
            'error',
            'from',
            'to'
        ));
    }

    /**
     * Answer an administrator's question using the supplied POS statistics.
     */
    public function ask(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'min:3', 'max:1000'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        // Reuse the same page's validated filters to build the data context.
        $sales = DB::table('sales')
            ->where('status', 'PAID')
            ->when($from, fn ($q) => $q->whereDate('sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sale_date', '<=', $to));

        $revenue = (float) (clone $sales)->sum('total');

        $cost = (float) DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($q) => $q->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sales.sale_date', '<=', $to))
            ->selectRaw('COALESCE(SUM(
                sale_items.quantity * COALESCE(
                    sale_items.purchase_price_at_sale,
                    products.purchase_price,
                    0
                )
            ), 0) AS total_cost')
            ->value('total_cost');

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($q) => $q->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sales.sale_date', '<=', $to))
            ->select('products.name')
            ->selectRaw('SUM(sale_items.quantity) AS quantity_sold')
            ->selectRaw('SUM(sale_items.quantity * sale_items.unit_price) AS revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity_sold')
            ->limit(10)
            ->get();

        $lowStock = DB::table('stock')
            ->join('products', 'products.id', '=', 'stock.product_id')
            ->whereColumn('stock.quantity', '<=', 'stock.min_threshold')
            ->select('products.name', 'stock.quantity', 'stock.min_threshold')
            ->orderBy('stock.quantity')
            ->limit(20)
            ->get();

        $currency = Parametre::first()?->currency ?? 'TND';

        $context = [
            'period' => ['from' => $from, 'to' => $to],
            'currency' => $currency,
            'total_sales' => (clone $sales)->count(),
            'revenue' => round($revenue, 3),
            'estimated_cost' => round($cost, 3),
            'gross_profit' => round($revenue - $cost, 3),
            'profit_margin_percent' => $revenue > 0
                ? round((($revenue - $cost) / $revenue) * 100, 2)
                : 0,
            'top_products' => $topProducts,
            'low_stock_products' => $lowStock,
        ];

        try {
            $answer = $this->askOllama($validated['question'], $context);
        } catch (\Throwable $exception) {
            Log::error('POS AI question failed: ' . $exception->getMessage());

            return redirect()
                ->route('admin.ai-insights.index', array_filter([
                    'from' => $from,
                    'to' => $to,
                ]))
                ->withInput()
                ->withErrors([
                    'question' => 'Could not contact Ollama. Check that the local AI service is running.',
                ]);
        }

        return redirect()
            ->route('admin.ai-insights.index', array_filter([
                'from' => $from,
                'to' => $to,
            ]))
            ->with('ai_answer', $answer)
            ->withInput(['question' => $validated['question']]);
    }

    /**
     * Send a question and trusted POS data to the local Ollama API.
     */
    private function askOllama(string $question, array $context): string
    {
        $response = Http::connectTimeout(10)
            ->timeout(300)
            ->post(
                rtrim(config('services.ollama.url', 'http://127.0.0.1:11434'), '/')
                    . '/api/generate',
                [
                    'model' => config('services.ollama.model', 'qwen2.5:3b'),
                    'stream' => false,
                    'prompt' => "You are an assistant for a retail POS administrator.\n"
                        . "Use only the supplied POS data for claims about this business.\n"
                        . "Never invent totals, products, or stock quantities.\n"
                        . "If information is missing, say so clearly.\n\n"
                        . "POS DATA:\n"
                        . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                        . "\n\nADMINISTRATOR REQUEST:\n"
                        . $question
                        . "\n\nGive a clear, concise and short if possible answer with practical recommendations where appropriate.",
                    'options' => [
                        'temperature' => 0.2,
                        'num_predict' => 100,
                    ],
                ]
            )
            ->throw()
            ->json();

        $answer = trim($response['response'] ?? '');

        if ($answer === '') {
            throw new \RuntimeException('Ollama returned an empty answer.');
        }

        return $answer;
    }
}
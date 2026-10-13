<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIInsightsController extends Controller
{
    /**
     * Display POS statistics and the administrator's conversations.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'conversation' => ['nullable', 'integer'],
        ]);

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        // Calculate the latest POS statistics.
        $data = $this->getPosData($from, $to);

        $insights = null;
        $error = null;

        // Generate an overview only when requested.
        if ($request->boolean('analyze')) {
            try {
                $insights = $this->askOllama(
                    'Analyze these POS statistics. Explain business performance, '
                    . 'identify important findings supported by the data, and suggest '
                    . 'practical improvements. Discuss best sellers and low-stock items. '
                    . 'Do not invent figures or unsupported trends. Explain that cost '
                    . 'is estimated using historical purchase prices when available, '
                    . 'otherwise current purchase prices. Be concise.',
                    $data
                );
            } catch (\Throwable $exception) {
                Log::error('POS AI overview failed: ' . $exception->getMessage());

                $error = 'Could not contact the local AI. Check that Ollama is running.';
            }
        }

        // Only retrieve conversations owned by the logged-in administrator.
        $conversations = AiConversation::query()
            ->where('user_id', auth()->id())
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        // Never load a conversation belonging to another administrator.
        $activeConversation = null;

        if (!empty($filters['conversation'])) {
            $activeConversation = AiConversation::query()
                ->where('user_id', auth()->id())
                ->with('messages')
                ->findOrFail($filters['conversation']);
        }

        return view('admin.ai-insights.index', [
            'data' => $data,
            'insights' => $insights,
            'error' => $error,
            'from' => $from,
            'to' => $to,
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
        ]);
    }

    /**
     * Save the question, send conversation history to Ollama,
     * and save the AI response.
     */
    public function ask(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'min:3', 'max:1000'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;
        $question = trim($validated['question']);

        // Use the supplied conversation only if it belongs to this admin.
        if (!empty($validated['conversation_id'])) {
            $conversation = AiConversation::query()
                ->where('user_id', auth()->id())
                ->findOrFail($validated['conversation_id']);
        } else {
            // Create a new conversation for the first question.
            $conversation = AiConversation::create([
                'user_id' => auth()->id(),
                'title' => Str::limit($question, 60, '...'),
            ]);
        }

        // Save the administrator's message before contacting Ollama.
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $question,
        ]);

        // Get current POS figures for this request.
        $context = $this->getPosData($from, $to);

        // Load recent conversation history, including the new question.
        // Limit the history so the prompt does not grow indefinitely.
        $history = $conversation->messages()
            ->orderByDesc('id')
            ->limit(12)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($message) => [
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->all();

        try {
            $answer = $this->askOllama(
                $question,
                $context,
                $history
            );

            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $answer,
            ]);

            // Update the conversation timestamp for sidebar ordering.
            $conversation->touch();
        } catch (\Throwable $exception) {
            Log::error('POS AI question failed: ' . $exception->getMessage());

            return redirect()
                ->route('admin.ai-insights.index', array_filter([
                    'from' => $from,
                    'to' => $to,
                    'conversation' => $conversation->id,
                ]))
                ->withInput()
                ->withErrors([
                    'question' => 'Could not contact Ollama. Your question was saved; check that Ollama is running and try again.',
                ]);
        }

        return redirect()->route('admin.ai-insights.index', array_filter([
            'from' => $from,
            'to' => $to,
            'conversation' => $conversation->id,
        ]));
    }

    /**
     * Calculate fresh statistics from the POS database.
     */
    private function getPosData(?string $from, ?string $to): array
    {
        // Only fully paid sales count toward financial statistics.
        $sales = DB::table('sales')
            ->where('status', 'PAID')
            ->when($from, fn ($query) => $query->whereDate('sale_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('sale_date', '<=', $to));

        $totalSales = (clone $sales)->count();
        $revenue = (float) (clone $sales)->sum('total');

        $cost = (float) DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($query) => $query->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('sales.sale_date', '<=', $to))
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
            ->when($from, fn ($query) => $query->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('sales.sale_date', '<=', $to))
            ->sum('sale_items.quantity');

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'PAID')
            ->when($from, fn ($query) => $query->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('sales.sale_date', '<=', $to))
            ->select('products.name')
            ->selectRaw('SUM(sale_items.quantity) AS quantity_sold')
            ->selectRaw('SUM(sale_items.quantity * sale_items.unit_price) AS revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity_sold')
            ->limit(10)
            ->get();

        // Inventory is current and is not restricted by sales dates.
        $lowStock = DB::table('stock')
            ->join('products', 'products.id', '=', 'stock.product_id')
            ->whereColumn('stock.quantity', '<=', 'stock.min_threshold')
            ->select('products.name', 'stock.quantity', 'stock.min_threshold')
            ->orderBy('stock.quantity')
            ->limit(20)
            ->get();

        return [
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
            'currency' => Parametre::first()?->currency ?? 'TND',
            'total_sales' => $totalSales,
            'revenue' => round($revenue, 3),
            'estimated_cost' => round($cost, 3),
            'gross_profit' => round($grossProfit, 3),
            'profit_margin_percent' => round($profitMargin, 2),
            'products_sold' => $productsSold,
            'top_products' => $topProducts,
            'low_stock_products' => $lowStock,
        ];
    }

    /**
     * Send current POS data and recent messages to Ollama.
     */
    private function askOllama(
        string $question,
        array $context,
        array $history = []
    ): string {
        $conversationText = '';

        foreach ($history as $message) {
            $speaker = $message['role'] === 'assistant'
                ? 'AI assistant'
                : 'Administrator';

            $conversationText .= $speaker . ': '
                . $message['content'] . "\n\n";
        }

        $prompt =
            "You are an intelligent business assistant for a retail POS system.\n"
            . "IMPORTANT LANGUAGE RULE:\n"
            . "Always answer in the same language explicitly requested by the administrator.\n"
            . "If the administrator asks you to speak Arabic, respond entirely in Arabic.\n"
            . "If the administrator asks for Tunisian Arabic, use natural Tunisian Arabic.\n"
            . "If the administrator asks for Modern Standard Arabic, use clear Modern Standard Arabic.\n"
            . "Remember the requested language throughout the conversation until the administrator changes it.\n"
            . "Do not switch to English unless explicitly requested.\n"
            . "Use only the supplied POS data for claims about this business.\n"
            . "Never invent financial figures, products, or stock quantities.\n"
            . "Use conversation history to understand follow-up questions, but prioritize current POS data.\n"
            . "If information is missing, say so clearly.\n\n"
            . "CURRENT POS DATA:\n"
            . json_encode(
                $context,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            )
            . "\n\nRECENT CONVERSATION:\n"
            . ($conversationText !== ''
                ? $conversationText
                : "No previous messages.\n")
            . ($history === []
                ? "\nADMINISTRATOR REQUEST:\n" . $question
                : '')
            . "\n\nAnswer clearly and concisely. Give practical recommendations "
            . "when appropriate. Distinguish facts from suggestions.";

        $response = Http::connectTimeout(10)
            ->timeout(300)
            ->post(
                rtrim(
                    config('services.ollama.url', 'http://127.0.0.1:11434'),
                    '/'
                ) . '/api/generate',
                [
                    'model' => config('services.ollama.model', 'qwen2.5:3b'),
                    'stream' => false,
                    'prompt' => $prompt,
                    'options' => [
                        'temperature' => 0.2,
                        'num_predict' => 300,
                    ],
                    'keep_alive' => '30m',
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
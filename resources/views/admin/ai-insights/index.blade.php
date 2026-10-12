@extends('layouts.app')

@section('content')
@php
    $currency = $data['currency'] ?? 'TND';

    $money = fn ($amount) =>
        number_format((float) $amount, 3) . ' ' . $currency;
@endphp

<div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">

        {{-- Page header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">
                    Administration / Analytics
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                    AI Business Insights
                </h1>

                <p class="mt-2 text-sm text-slate-600">
                    Understand your sales, profitability, and stock with local AI.
                </p>
            </div>

            <div class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Local AI · Ollama
            </div>
        </div>

        {{-- Date filters --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET"
                  action="{{ route('admin.ai-insights.index') }}"
                  class="grid grid-cols-1 items-end gap-4 sm:grid-cols-3">

                <div>
                    <label for="from"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        From date
                    </label>

                    <input type="date"
                           id="from"
                           name="from"
                           value="{{ $from }}"
                           class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                </div>

                <div>
                    <label for="to"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        To date
                    </label>

                    <input type="date"
                           id="to"
                           name="to"
                           value="{{ $to }}"
                           class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                        Apply filters
                    </button>

                    <a href="{{ route('admin.ai-insights.index') }}"
                       class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Financial summary --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Revenue</p>

                <p class="mt-3 text-2xl font-bold text-slate-900">
                    {{ $money($data['revenue']) }}
                </p>

                <p class="mt-2 text-xs text-slate-500">
                    Paid sales only
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Estimated cost</p>

                <p class="mt-3 text-2xl font-bold text-slate-900">
                    {{ $money($data['estimated_cost']) }}
                </p>

                <p class="mt-2 text-xs text-slate-500">
                    Based on recorded purchase prices
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Gross profit</p>

                <p class="mt-3 text-2xl font-bold {{ $data['gross_profit'] < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                    {{ $money($data['gross_profit']) }}
                </p>

                <p class="mt-2 text-xs text-slate-500">
                    Revenue minus estimated cost
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Profit margin</p>

                <p class="mt-3 text-2xl font-bold text-slate-900">
                    {{ number_format($data['profit_margin_percent'], 2) }}%
                </p>

                <p class="mt-2 text-xs text-slate-500">
                    Gross profit as a percentage of revenue
                </p>
            </div>
        </div>

        {{-- Sales and inventory --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Top products --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="font-semibold text-slate-900">
                        Best-selling products
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Ranked by quantity sold during the selected period.
                    </p>
                </div>

                @if ($data['top_products']->isEmpty())
                    <div class="p-6 text-sm text-slate-500">
                        No paid product sales were found for this period.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">Product</th>
                                    <th class="px-5 py-3 text-right">Quantity</th>
                                    <th class="px-5 py-3 text-right">Revenue</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @foreach ($data['top_products'] as $product)
                                    <tr>
                                        <td class="px-5 py-4 font-medium text-slate-800">
                                            {{ $product->name }}
                                        </td>

                                        <td class="px-5 py-4 text-right text-slate-600">
                                            {{ $product->quantity_sold }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right text-slate-600">
                                            {{ $money($product->revenue) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Low stock --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="font-semibold text-slate-900">
                        Low-stock alerts
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Products at or below their minimum stock threshold.
                    </p>
                </div>

                @if ($data['low_stock_products']->isEmpty())
                    <div class="p-6 text-sm text-emerald-700">
                        No products are currently below their stock threshold.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">Product</th>
                                    <th class="px-5 py-3 text-right">In stock</th>
                                    <th class="px-5 py-3 text-right">Minimum</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @foreach ($data['low_stock_products'] as $product)
                                    <tr>
                                        <td class="px-5 py-4 font-medium text-slate-800">
                                            {{ $product->name }}
                                        </td>

                                        <td class="px-5 py-4 text-right font-semibold text-red-600">
                                            {{ $product->quantity }}
                                        </td>

                                        <td class="px-5 py-4 text-right text-slate-600">
                                            {{ $product->min_threshold }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Generate AI analysis --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        AI business analysis
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Ask Ollama to interpret the figures calculated by your POS.
                    </p>
                </div>

                <form method="GET" action="{{ route('admin.ai-insights.index') }}">
                    @if ($from)
                        <input type="hidden" name="from" value="{{ $from }}">
                    @endif

                    @if ($to)
                        <input type="hidden" name="to" value="{{ $to }}">
                    @endif

                    <input type="hidden" name="analyze" value="1">

                    <button type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">
                        Generate AI analysis
                    </button>
                </form>
            </div>

            @if ($error)
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ $error }}
                </div>
            @endif

            @if ($insights)
                <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <div class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        AI analysis
                    </div>

                    <div class="whitespace-pre-wrap text-sm leading-7 text-slate-700">{{ $insights }}</div>
                </div>
            @endif
        </div>

        {{-- Ask the AI --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900">
                Ask your POS assistant
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Ask a question about sales, profitability, best sellers, or stock.
            </p>

            <form method="POST"
                  action="{{ route('admin.ai-insights.ask') }}"
                  class="mt-5 space-y-4">

                @csrf

                <div>
                    <label for="question"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        Your question
                    </label>

                    <textarea id="question"
                              name="question"
                              rows="3"
                              maxlength="1000"
                              required
                              placeholder="Example: Which products should I prioritize when restocking?"
                              class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200">{{ old('question') }}</textarea>
                </div>

                @if ($from)
                    <input type="hidden" name="from" value="{{ $from }}">
                @endif

                @if ($to)
                    <input type="hidden" name="to" value="{{ $to }}">
                @endif

                <button type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">
                    Ask AI
                </button>
            </form>

            @if (session('ai_answer'))
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50/50 p-5">
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">
                        AI response
                    </h3>

                    <div class="whitespace-pre-wrap text-sm leading-7 text-slate-700">{{ session('ai_answer') }}</div>
                </div>
            @endif
        </div>

        <p class="text-xs leading-5 text-slate-500">
            Financial figures are calculated by the application. AI responses are
            recommendations, not guaranteed business forecasts. Estimated costs
            may use current purchase prices when historical purchase prices are missing.
        </p>

    </div>
</div>
@endsection
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

        {{-- AI Chat Interface --}}
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    {{-- Chat header --}}
    <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900">
                POS AI Assistant
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Your personal business analyst, with conversation memory.
            </p>
        </div>

        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            Ollama · Local AI
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[260px_minmax(0,1fr)]">

        {{-- Sidebar --}}
        <aside class="border-b border-slate-200 bg-slate-50 p-4 md:border-b-0 md:border-r">
            <a href="{{ route('admin.ai-insights.index', array_filter([
                    'from' => $from,
                    'to' => $to,
                ])) }}"
               class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">

                <span class="text-lg leading-none">+</span>
                New chat
            </a>

            <h3 class="mb-3 mt-6 text-xs font-semibold uppercase tracking-widest text-slate-400">
                Recent conversations
            </h3>

            <div class="max-h-[420px] space-y-2 overflow-y-auto">

                @forelse ($conversations as $conversation)
                    <a href="{{ route('admin.ai-insights.index', array_filter([
                            'conversation' => $conversation->id,
                            'from' => $from,
                            'to' => $to,
                        ])) }}"
                       class="block rounded-xl border p-3 transition
                       {{ $activeConversation?->id === $conversation->id
                            ? 'border-slate-400 bg-white shadow-sm'
                            : 'border-transparent hover:border-slate-200 hover:bg-white' }}">

                        <p class="truncate text-sm font-medium text-slate-800">
                            {{ $conversation->title }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            {{ $conversation->messages_count }}
                            {{ $conversation->messages_count === 1 ? 'message' : 'messages' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            {{ $conversation->updated_at->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 bg-white p-4 text-sm leading-6 text-slate-500">
                        No conversations yet. Ask your first question to start one.
                    </p>
                @endforelse

            </div>
        </aside>

        {{-- Main chat --}}
        <section class="min-w-0">

            {{-- Conversation title --}}
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="font-semibold text-slate-900">
                    {{ $activeConversation?->title ?? 'New conversation' }}
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Answers use current POS figures and recent conversation history.
                </p>
            </div>

            {{-- Messages --}}
            <div id="ai-chat-messages"
                 class="max-h-[600px] min-h-[300px] space-y-5 overflow-y-auto bg-slate-50/70 p-5">

                @if ($activeConversation && $activeConversation->messages->isNotEmpty())

                    @foreach ($activeConversation->messages as $message)

                        @if ($message->role === 'user')
                            <div class="flex justify-end">
                                <div class="max-w-[90%] rounded-2xl rounded-br-md bg-slate-900 px-4 py-3 text-sm leading-6 text-white shadow-sm">
                                    <p class="mb-1 text-xs font-semibold text-slate-300">
                                        You
                                    </p>

                                    <div class="whitespace-pre-wrap break-words">{{ $message->content }}</div>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="max-w-[95%] rounded-2xl rounded-bl-md border border-slate-200 bg-white px-4 py-4 shadow-sm">
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-emerald-700">
                                        POS AI Assistant
                                    </p>

                                    <div class="whitespace-pre-wrap break-words text-sm leading-7 text-slate-700">{{ $message->content }}</div>
                                </div>
                            </div>
                        @endif

                    @endforeach

                @else

                    <div class="flex min-h-[260px] flex-col items-center justify-center px-4 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm">
                            💬
                        </div>

                        <h4 class="mt-4 font-semibold text-slate-800">
                            What would you like to know?
                        </h4>

                        <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500">
                            Ask about revenue, profitability, best-selling products,
                            stock levels, or ways to improve your business.
                        </p>
                    </div>

                @endif

            </div>

            {{-- Suggested prompts --}}
            <div class="border-t border-slate-200 px-5 pt-4">
                <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Suggested questions
                </p>

                <div class="flex flex-wrap gap-2">
                    <button type="button"
                            onclick="setAIQuestion('Which products generate the most revenue?')"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50">
                        Best revenue products
                    </button>

                    <button type="button"
                            onclick="setAIQuestion('How can I improve my gross profit?')"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50">
                        Improve profitability
                    </button>

                    <button type="button"
                            onclick="setAIQuestion('Which products should I prioritize when restocking?')"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50">
                        Restocking advice
                    </button>
                </div>
            </div>

            {{-- Question form --}}
            <div class="bg-white p-5">
                <form method="POST"
                      action="{{ route('admin.ai-insights.ask') }}"
                      class="space-y-3">

                    @csrf

                    @if ($from)
                        <input type="hidden" name="from" value="{{ $from }}">
                    @endif

                    @if ($to)
                        <input type="hidden" name="to" value="{{ $to }}">
                    @endif

                    @if ($activeConversation)
                        <input type="hidden"
                               name="conversation_id"
                               value="{{ $activeConversation->id }}">
                    @endif

                    <label for="question"
                           class="block text-sm font-semibold text-slate-700">
                        Your message
                    </label>

                    <textarea id="question"
                              name="question"
                              rows="3"
                              maxlength="1000"
                              required
                              placeholder="Ask a question or follow up on an earlier answer..."
                              class="w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-800 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-100">{{ old('question') }}</textarea>

                    @error('question')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-slate-400">
                            AI responses can be inaccurate. Verify important decisions.
                        </p>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-200">
                            Send message
                            <span aria-hidden="true">↑</span>
                        </button>
                    </div>

                </form>
            </div>

        </section>
    </div>
</div>

{{-- Chat helpers --}}
<script>
    function setAIQuestion(question) {
        const textarea = document.getElementById('question');

        if (textarea) {
            textarea.value = question;
            textarea.focus();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const messages = document.getElementById('ai-chat-messages');

        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    });
</script>
@endsection
{{-- Expects: $session (open CashSession or null), $sales (sales of the open session) --}}
@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@section('content')
@php
    $paidSales = $sales->where('status', 'PAID');
    $pendingSales = $sales->where('status', 'PENDING');

    $sessionRevenue = $paidSales->sum('total');
    $totalTransactions = $paidSales->count();
    $pendingCount = $pendingSales->count();
    
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';

@endphp

<div class="space-y-8">

    {{-- Page heading --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-600">CASHIER WORKSPACE</p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
                Cashier Dashboard
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Manage your register, transactions, and daily sales.
            </p>
        </div>

        <div class="text-sm text-slate-500">
            <i class="fa-regular fa-calendar mr-2"></i>
            {{ now()->format('d M Y') }}
        </div>
    </div>


    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    @endif


    {{-- No active session --}}
    @if (! $session)

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 p-6 sm:p-8">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i class="fa-solid fa-cash-register text-xl"></i>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            Your register is closed
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Open a cash session before creating sales.
                            Enter the amount of cash currently in your drawer.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <form
                    method="POST"
                    action="{{ route('cashier.sessions.store') }}"
                    class="max-w-xl space-y-5"
                >
                    @csrf

                    <div>
                        <label for="opening_amount" class="mb-2 block text-sm font-semibold text-slate-700">
                            Opening cash amount
                        </label>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-medium text-slate-400">
                                {{ $currency }}
                            </span>

                            <input
                                id="opening_amount"
                                name="opening_amount"
                                type="number"
                                step="0.001"
                                min="0"
                                value="{{ old('opening_amount', '0.000') }}"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-16 pr-4 text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                placeholder="0.000"
                            >
                        </div>

                        @error('opening_amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-2 text-xs text-slate-500">
                            Count the cash physically present in the drawer.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 sm:w-auto"
                    >
                        <i class="fa-solid fa-lock-open"></i>
                        Open Cash Session
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-xl border border-blue-100 bg-blue-50 p-5">
            <div class="flex gap-3">
                <i class="fa-solid fa-circle-info mt-0.5 text-blue-600"></i>

                <div>
                    <h3 class="font-semibold text-blue-900">Before you start</h3>

                    <p class="mt-1 text-sm leading-6 text-blue-800">
                        Verify the opening cash amount before accepting payments.
                        Keep your register secure throughout the session.
                    </p>
                </div>
            </div>
        </div>

    @else

        {{-- Active session header --}}
        <div class="overflow-hidden rounded-2xl bg-slate-900 text-white shadow-lg">
            <div class="flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            REGISTER OPEN
                        </span>

                        <span class="text-xs text-slate-400">
                            Session #{{ $session->id }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-bold">
                        Your register is ready
                    </h2>

                    <p class="mt-2 text-sm text-slate-400">
                        Opened at {{ $session->opened_at }}
                    </p>
                </div>

                <form method="POST" action="{{ route('cashier.sales.store') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-500 px-6 py-3 font-semibold text-white transition hover:bg-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-400/30 sm:w-auto"
                    >
                        <i class="fa-solid fa-cart-shopping"></i>
                        Start New Sale
                        <i class="fa-solid fa-arrow-right ml-1"></i>
                    </button>
                </form>

            </div>
        </div>


        {{-- Session information --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Opening amount --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Opening Cash</p>

                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-wallet"></i>
                    </span>
                </div>

                <p class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                    {{ number_format($session->opening_amount, 3) }}
                    <span class="text-sm font-medium text-slate-500">{{ $currency }}</span>
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Amount at session opening
                </p>
            </div>


            {{-- Revenue --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Paid Revenue</p>

                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </span>
                </div>

                <p class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                    {{ number_format($sessionRevenue, 3) }}
                    <span class="text-sm font-medium text-slate-500">{{ $currency }}</span>
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    From completed sales in this session
                </p>
            </div>


            {{-- Transactions --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Completed Sales</p>

                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                        <i class="fa-solid fa-receipt"></i>
                    </span>
                </div>

                <p class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                    {{ $totalTransactions }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Fully paid transactions
                </p>
            </div>


            {{-- Pending --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Pending Sales</p>

                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fa-solid fa-clock"></i>
                    </span>
                </div>

                <p class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                    {{ $pendingCount }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Sales awaiting completion
                </p>
            </div>

        </div>


        {{-- Sales history --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Recent Transactions
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Sales associated with your current cash session.
                    </p>
                </div>

                <span class="self-start rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 sm:self-auto">
                    {{ $sales->count() }} {{ \Illuminate\Support\Str::plural('transaction', $sales->count()) }}
                </span>
            </div>


            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-5 py-4 font-semibold">Sale ID</th>
                            <th class="whitespace-nowrap px-5 py-4 font-semibold">Date</th>
                            <th class="whitespace-nowrap px-5 py-4 text-right font-semibold">Total</th>
                            <th class="whitespace-nowrap px-5 py-4 font-semibold">Status</th>
                            <th class="whitespace-nowrap px-5 py-4 text-right font-semibold">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($sales as $sale)
                            <tr class="transition hover:bg-slate-50">

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="font-semibold text-slate-900">
                                        #{{ $sale->id }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-slate-500">
                                    {{ $sale->sale_date }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900">
                                    {{ number_format($sale->total, 3) }}
                                    <span class="text-xs font-normal text-slate-500">{{ $currency }}</span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @include('partials.status-badge', ['status' => $sale->status])
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a
                                        href="{{ route('cashier.sales.show', $sale) }}"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                    >
                                        View
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <i class="fa-solid fa-receipt text-xl"></i>
                                    </div>

                                    <h3 class="mt-4 font-semibold text-slate-900">
                                        No transactions yet
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Start your first sale to see it appear here.
                                    </p>

                                    <form
                                        method="POST"
                                        action="{{ route('cashier.sales.store') }}"
                                        class="mt-4"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                                        >
                                            <i class="fa-solid fa-plus"></i>
                                            Create First Sale
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>


        {{-- Close session --}}
        <div class="rounded-2xl border border-red-200 bg-white shadow-sm">

            <div class="flex items-start gap-4 p-5 sm:p-6">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <div class="flex-1">
                    <h2 class="font-bold text-slate-900">
                        Close Cash Session
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Count the cash remaining in your drawer before closing.
                        Enter the actual amount, not the expected amount.
                    </p>
                </div>
            </div>

            <div class="border-t border-slate-100 p-5 sm:p-6">
                <form
                    method="POST"
                    action="{{ route('cashier.sessions.close', $session) }}"
                    class="flex flex-col gap-4 sm:flex-row sm:items-end"
                    onsubmit="return confirm('Are you sure you want to close this cash session?');"
                >
                    @csrf
                    @method('PATCH')

                    <div class="w-full sm:max-w-sm">
                        <label for="closing_amount" class="mb-2 block text-sm font-semibold text-slate-700">
                            Counted cash in drawer ({{ $currency }})
                        </label>

                        <input
                            id="closing_amount"
                            name="closing_amount"
                            type="number"
                            step="0.001"
                            min="0"
                            value="{{ old('closing_amount') }}"
                            required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-500/10"
                            placeholder="0.000"
                        >

                        @error('closing_amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-5 py-3 font-semibold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-500/10"
                    >
                        <i class="fa-solid fa-lock"></i>
                        Close Session
                    </button>
                </form>
            </div>
        </div>

    @endif

</div>
@endsection
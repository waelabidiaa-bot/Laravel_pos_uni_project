{{-- Expects: $sale (with items.product, payments). Main POS screen. --}}
@extends('layouts.app')

@section('title', 'Checkout — Sale #' . $sale->id)

@php
    $isPending = $sale->status === 'PENDING';
    $paid = $sale->payments->sum('amount');
    $remaining = max(0, $sale->total - $paid);
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';
@endphp

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a
                href="{{ route('cashier.dashboard') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back to dashboard
            </a>

            <div class="mt-3 flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                    Checkout
                </h1>

                @include('partials.status-badge', ['status' => $sale->status])
            </div>

            <p class="mt-1 text-sm text-slate-500">
                Transaction #{{ $sale->id }}
                @if ($sale->sale_date)
                    · {{ $sale->sale_date }}
                @endif
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                Transaction status
            </p>
            <p class="mt-1 font-semibold text-slate-900">
                {{ $sale->status }}
            </p>
        </div>
    </div>


    {{-- Flash messages --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            <i class="fa-solid fa-circle-check mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>
            {{ session('error') }}
        </div>
    @endif


    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="font-semibold text-red-800">Please check the following:</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    

    <div class="grid items-start gap-6 xl:grid-cols-3">

    {{-- Left: products and shopping cart --}}
    <section class="min-w-0 space-y-5 xl:col-span-2">
        <livewire:cashier.pos-cart :sale="$sale" />
    </section>

    {{-- Right: payment and order summary --}}
    <aside class="min-w-0 space-y-5">
        <livewire:cashier.payment-summary :sale="$sale" />
        {{-- Payment history --}}
            @if ($sale->payments->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="font-bold text-slate-900">Payment History</h2>

                    <div class="mt-4 space-y-3">
                        @foreach ($sale->payments as $payment)
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                        <i class="{{ $payment->method === 'CARD' ? 'fa-solid fa-credit-card' : 'fa-solid fa-money-bill-wave' }}"></i>
                                    </span>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $payment->method }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            Payment received
                                        </p>
                                    </div>
                                </div>

                                <span class="whitespace-nowrap text-sm font-semibold text-slate-900">
                                    {{ number_format($payment->amount, 3) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif


            {{-- Completed sale actions --}}
            @if ($sale->status === 'PAID')
                <a
                    href="{{ route('cashier.sales.receipt', $sale) }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-700 px-5 py-3 font-semibold text-white transition hover:bg-slate-800"
                >
                    <i class="fa-solid fa-print"></i>
                    Print Receipt
                </a>

                <form method="POST" action="{{ route('cashier.sales.store') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white transition hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-plus"></i>
                        Start New Sale
                    </button>
                </form>
            @endif


            {{-- Cancel pending sale --}}
            @if ($isPending)
                <form
                    method="POST"
                    action="{{ route('cashier.sales.cancel', $sale) }}"
                    onsubmit="return confirm('Are you sure you want to cancel this sale?')"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-5 py-3 font-semibold text-red-700 transition hover:bg-red-50"
                    >
                        <i class="fa-solid fa-ban"></i>
                        Cancel Sale
                    </button>
                </form>
            @endif

        </aside>

        {{-- Keep your existing payment form here --}}
        {{-- Keep your existing payment history here --}}
        {{-- Keep your existing cancel-sale controls here --}}
    </aside>

    </div>


            

    

</div>
@endsection
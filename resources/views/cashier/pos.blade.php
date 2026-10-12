{{-- Expects: $sale (with items.product, payments). Main POS screen. --}}
@extends('layouts.app')

@section('title', 'Checkout — Sale #' . $sale->id)

@php
    $isPending = $sale->status === 'PENDING';
    $paid = $sale->payments->sum('amount');
    $remaining = max(0, $sale->total - $paid);
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

        {{-- Product/cart area --}}
        <section class="space-y-5 xl:col-span-2">

            {{-- Add product --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-barcode text-lg"></i>
                    </div>

                    <div>
                        <h2 class="font-bold text-slate-900">Add a product</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Scan a barcode or enter a product name.
                        </p>
                    </div>
                </div>

                @if ($isPending)
                    <form
                        method="POST"
                        action="{{ route('cashier.sales.items.store', $sale) }}"
                        class="space-y-4 sm:flex sm:items-end sm:gap-3 sm:space-y-0"
                    >
                        @csrf

                        <div class="min-w-0 flex-1">
                            <label for="barcode" class="mb-2 block text-sm font-semibold text-slate-700">
                                Barcode or product name
                            </label>

                            <input
                                id="barcode"
                                name="barcode"
                                type="text"
                                value="{{ old('barcode') }}"
                                placeholder="Scan barcode or type a name..."
                                required
                                autofocus
                                autocomplete="off"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                            >

                            @error('barcode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:w-24">
                            <label for="quantity" class="mb-2 block text-sm font-semibold text-slate-700">
                                Quantity
                            </label>

                            <input
                                id="quantity"
                                name="quantity"
                                type="number"
                                min="1"
                                step="1"
                                value="{{ old('quantity', 1) }}"
                                required
                                class="w-full rounded-xl border border-slate-200 px-3 py-3 text-center text-slate-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >

                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 sm:w-auto"
                        >
                            <i class="fa-solid fa-plus"></i>
                            Add
                        </button>
                    </form>
                @else
                    <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                        <i class="fa-solid fa-lock mr-2"></i>
                        This sale is no longer pending, so products cannot be added.
                    </div>
                @endif
            </div>


            {{-- Shopping cart --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between border-b border-slate-100 p-5 sm:p-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Shopping Cart</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $sale->items->count() }}
                            {{ \Illuminate\Support\Str::plural('product', $sale->items->count()) }}
                        </p>
                    </div>

                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold">Product</th>
                                <th class="whitespace-nowrap px-5 py-4 text-right font-semibold">Qty</th>
                                <th class="whitespace-nowrap px-5 py-4 text-right font-semibold">Unit price</th>
                                <th class="whitespace-nowrap px-5 py-4 text-right font-semibold">Subtotal</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($sale->items as $item)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900">
                                            {{ $item->product->name ?? 'Product unavailable' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Product #{{ $item->product_id }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-slate-600">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right text-slate-600">
                                        {{ number_format($item->unit_price, 3) }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900">
                                        {{ number_format($item->quantity * $item->unit_price, 3) }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        @if ($isPending)
                                            <form
                                                method="POST"
                                                action="{{ route('cashier.sales.items.destroy', [$sale, $item->product_id]) }}"
                                                onsubmit="return confirm('Remove this product from the sale?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    aria-label="Remove product"
                                                    title="Remove product"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                                                >
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-14 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                            <i class="fa-solid fa-basket-shopping text-xl"></i>
                                        </div>

                                        <h3 class="mt-4 font-semibold text-slate-900">
                                            Your cart is empty
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Scan a product or enter its name to begin.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($sale->items->isNotEmpty())
                    <div class="border-t border-slate-100 bg-slate-50 px-5 py-4 text-sm text-slate-500">
                        <i class="fa-solid fa-circle-info mr-2"></i>
                        Check product quantities and prices before accepting payment.
                    </div>
                @endif

            </div>
        </section>


        {{-- Order summary/payment --}}
        <aside class="space-y-5">

            {{-- Total --}}
            <div class="overflow-hidden rounded-2xl bg-slate-900 text-white shadow-lg">
                <div class="p-6">
                    <p class="text-sm font-medium text-slate-400">Amount to pay</p>

                    <p class="mt-3 break-words text-3xl font-bold tracking-tight sm:text-4xl">
                        {{ number_format($sale->total, 3) }}
                    </p>

                    <p class="mt-1 text-sm text-slate-400">TND</p>

                    <div class="my-6 border-t border-white/10"></div>

                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-slate-400">Total paid</span>
                        <span class="font-semibold">
                            {{ number_format($paid, 3) }} TND
                        </span>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-3 text-sm">
                        <span class="text-slate-400">Remaining</span>
                        <span class="font-semibold {{ $remaining > 0 ? 'text-amber-300' : 'text-emerald-300' }}">
                            {{ number_format($remaining, 3) }} TND
                        </span>
                    </div>
                </div>

                @if ($sale->status === 'PAID')
                    <div class="bg-emerald-500/10 px-6 py-4 text-sm font-semibold text-emerald-300">
                        <i class="fa-solid fa-circle-check mr-2"></i>
                        Payment completed
                    </div>
                @elseif ($sale->status === 'CANCELLED')
                    <div class="bg-red-500/10 px-6 py-4 text-sm font-semibold text-red-300">
                        <i class="fa-solid fa-circle-xmark mr-2"></i>
                        This sale was cancelled
                    </div>
                @endif
            </div>


            {{-- Payment form --}}
            @if ($isPending && $sale->items->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>

                        <div>
                            <h2 class="font-bold text-slate-900">Take Payment</h2>
                            <p class="mt-1 text-xs text-slate-500">Complete the transaction.</p>
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('cashier.sales.payments.store', $sale) }}"
                        class="space-y-4"
                    >
                        @csrf

                        <div>
                            <label for="method" class="mb-2 block text-sm font-semibold text-slate-700">
                                Payment method
                            </label>

                            <select
                                id="method"
                                name="method"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                                <option value="CASH" @selected(old('method', 'CASH') === 'CASH')>
                                    Cash
                                </option>

                                <option value="CARD" @selected(old('method') === 'CARD')>
                                    Card
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="amount" class="mb-2 block text-sm font-semibold text-slate-700">
                                Amount received (TND)
                            </label>

                            <input
                                id="amount"
                                name="amount"
                                type="number"
                                step="0.001"
                                min="0.001"
                                value="{{ old('amount', number_format($remaining, 3, '.', '')) }}"
                                required
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-lg font-semibold text-slate-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >

                            @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Follow the amount and payment rules enforced by your application.
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                        >
                            <i class="fa-solid fa-check"></i>
                            Submit Payment
                        </button>
                    </form>
                </div>
            @endif


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

    </div>

</div>
@endsection
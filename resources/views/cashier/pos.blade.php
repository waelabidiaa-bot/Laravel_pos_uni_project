{{-- Expects: $sale (with items.product, payments). Main POS screen. --}}
@extends('layouts.app')
@section('title', 'Sale #' . $sale->id)

@php
    $isPending = $sale->status === 'PENDING';
    $paid      = $sale->payments->sum('amount');
    $remaining = max(0, $sale->total - $paid);
@endphp

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Sale #{{ $sale->id }} @include('partials.status-badge', ['status' => $sale->status])</h1>
    <a href="{{ route('cashier.dashboard') }}" class="text-sky-600">← Back to session</a>
</div>

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Items --}}
    <div class="lg:col-span-2">
        @if ($isPending)
            <form method="POST" action="{{ route('cashier.sales.items.store', $sale) }}" class="flex gap-3 mb-4">
                @csrf
                <input name="barcode" placeholder="Scan or type barcode / product name" required autofocus
                       class="flex-1 border rounded px-3 py-2">
                <input name="quantity" type="number" min="1" value="1" class="w-20 border rounded px-3 py-2">
                <button class="bg-sky-600 hover:bg-sky-700 text-white rounded px-4 py-2">Add</button>
            </form>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                    <tr><th class="p-3">Product</th><th class="p-3 text-right">Qty</th>
                        <th class="p-3 text-right">Unit price</th><th class="p-3 text-right">Subtotal</th><th class="p-3"></th></tr>
                </thead>
                <tbody>
                    @forelse ($sale->items as $item)
                        <tr class="border-t">
                            <td class="p-3">{{ $item->product->name }}</td>
                            <td class="p-3 text-right">{{ $item->quantity }}</td>
                            <td class="p-3 text-right">{{ number_format($item->unit_price, 3) }}</td>
                            <td class="p-3 text-right">{{ number_format($item->quantity * $item->unit_price, 3) }}</td>
                            <td class="p-3 text-right">
                                @if ($isPending)
                                    <form method="POST" action="{{ route('cashier.sales.items.destroy', [$sale, $item->product_id]) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600">Remove</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">Cart is empty. Scan a product to start.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Totals and payment --}}
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow p-4 space-y-1">
            <div class="flex justify-between"><span class="text-gray-500">Total</span><span class="text-2xl font-bold">{{ number_format($sale->total, 3) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Paid</span><span>{{ number_format($paid, 3) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Remaining</span><span class="font-semibold">{{ number_format($remaining, 3) }}</span></div>
        </div>

        @if ($isPending && $sale->items->isNotEmpty())
            <form method="POST" action="{{ route('cashier.sales.payments.store', $sale) }}" class="bg-white rounded-lg shadow p-4 space-y-3">
                @csrf
                <h2 class="font-semibold">Take payment</h2>
                <select name="method" class="w-full border rounded px-3 py-2">
                    <option value="CASH">Cash</option>
                    <option value="CARD">Card</option>
                </select>
                <input name="amount" type="number" step="0.001" min="0.001" value="{{ number_format($remaining, 3, '.', '') }}"
                       class="w-full border rounded px-3 py-2">
                <button class="w-full bg-green-600 hover:bg-green-700 text-white rounded py-2">Pay</button>
            </form>
        @endif

        @if ($sale->payments->isNotEmpty())
            <div class="bg-white rounded-lg shadow p-4 text-sm">
                <h2 class="font-semibold mb-2">Payments</h2>
                @foreach ($sale->payments as $payment)
                    <div class="flex justify-between"><span>{{ $payment->method }}</span><span>{{ number_format($payment->amount, 3) }}</span></div>
                @endforeach
            </div>
        @endif

        @if ($sale->status === 'PAID')
            <a href="{{ route('cashier.sales.receipt', $sale) }}" target="_blank"
               class="block text-center bg-slate-700 hover:bg-slate-800 text-white rounded py-2">Print receipt</a>
            <form method="POST" action="{{ route('cashier.sales.store') }}">
                @csrf
                <button class="w-full bg-sky-600 hover:bg-sky-700 text-white rounded py-2">+ New sale</button>
            </form>
        @endif

        @if ($isPending)
            <form method="POST" action="{{ route('cashier.sales.cancel', $sale) }}" onsubmit="return confirm('Cancel this sale?')">
                @csrf @method('PATCH')
                <button class="w-full border border-red-300 text-red-700 hover:bg-red-50 rounded py-2">Cancel sale</button>
            </form>
        @endif
    </div>
</div>
@endsection

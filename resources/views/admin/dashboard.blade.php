{{-- Expects: $stats [sales_today, revenue_today, low_stock, cashiers], $latestSales (with cashSession.cashier) --}}
@extends('layouts.app')
@section('title', 'Admin dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Admin dashboard</h1>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach ([
        ['Sales today', $stats['sales_today']],
        ['Revenue today', number_format($stats['revenue_today'], 3) . ' TND'],
        ['Low-stock products', $stats['low_stock']],
        ['Cashiers', $stats['cashiers']],
    ] as [$label, $value])
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">{{ $label }}</div>
            <div class="text-2xl font-semibold">{{ $value }}</div>
        </div>
    @endforeach
</div>

<h2 class="font-semibold mb-2">Latest sales</h2>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="p-3">#</th><th class="p-3">Cashier</th><th class="p-3">Date</th><th class="p-3 text-right">Total</th><th class="p-3">Status</th></tr>
        </thead>
        <tbody>
            @forelse ($latestSales as $sale)
                <tr class="border-t">
                    <td class="p-3"><a class="text-sky-600" href="{{ route('admin.sales.show', $sale) }}">{{ $sale->id }}</a></td>
                    <td class="p-3">{{ $sale->cashSession->cashier->name }}</td>
                    <td class="p-3">{{ $sale->sale_date }}</td>
                    <td class="p-3 text-right">{{ number_format($sale->total, 3) }}</td>
                    <td class="p-3">@include('partials.status-badge', ['status' => $sale->status])</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-3 text-gray-500">No sales yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

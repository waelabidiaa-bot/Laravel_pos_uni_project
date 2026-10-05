{{-- Expects: $sales (paginator of Sale with cashSession.cashier) --}}
@extends('layouts.app')
@section('title', 'Sales')

@section('content')
<h1 class="text-2xl font-bold mb-6">Sales</h1>

<form method="GET" class="flex gap-3 mb-4">
    <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-3 py-2">
    <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-3 py-2">
    <select name="status" class="border rounded px-3 py-2">
        <option value="">All statuses</option>
        @foreach (['PENDING', 'PAID', 'CANCELLED'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
        @endforeach
    </select>
    <button class="bg-slate-700 text-white rounded px-4 py-2">Filter</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="p-3">#</th><th class="p-3">Cashier</th><th class="p-3">Date</th><th class="p-3 text-right">Total</th><th class="p-3">Status</th></tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr class="border-t">
                    <td class="p-3"><a class="text-sky-600" href="{{ route('admin.sales.show', $sale) }}">{{ $sale->id }}</a></td>
                    <td class="p-3">{{ $sale->cashSession->cashier->name }}</td>
                    <td class="p-3">{{ $sale->sale_date }}</td>
                    <td class="p-3 text-right">{{ number_format($sale->total, 3) }}</td>
                    <td class="p-3">@include('partials.status-badge', ['status' => $sale->status])</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sales->withQueryString()->links() }}</div>
@endsection

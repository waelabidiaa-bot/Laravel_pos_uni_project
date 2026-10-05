{{-- Expects: $sale (with items.product, payments, cashSession.cashier) --}}
@extends('layouts.app')
@section('title', 'Sale #' . $sale->id)

@section('content')
<a href="{{ route('admin.sales.index') }}" class="text-sky-600 text-sm">← Back to sales</a>
<h1 class="text-2xl font-bold my-4">Sale #{{ $sale->id }} @include('partials.status-badge', ['status' => $sale->status])</h1>
<p class="text-gray-500 mb-6">Cashier: {{ $sale->cashSession->cashier->name }} · {{ $sale->sale_date }}</p>
@include('partials.sale-summary', ['sale' => $sale])
@endsection

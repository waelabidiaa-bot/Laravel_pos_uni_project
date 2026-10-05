{{-- Expects: $session (open CashSession or null), $sales (sales of the open session, latest first) --}}
@extends('layouts.app')
@section('title', 'My session')

@section('content')
<h1 class="text-2xl font-bold mb-6">My cash session</h1>

@if (! $session)
    <div class="bg-white rounded-lg shadow p-6 max-w-md">
        <h2 class="font-semibold mb-3">Open a session</h2>
        <form method="POST" action="{{ route('cashier.sessions.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm mb-1">Opening cash amount (TND)</label>
                <input name="opening_amount" type="number" step="0.001" min="0" required class="w-full border rounded px-3 py-2">
            </div>
            <button class="bg-green-600 hover:bg-green-700 text-white rounded px-4 py-2">Open session</button>
        </form>
    </div>
@else
    <div class="grid md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Opened at</div>
            <div class="font-semibold">{{ $session->opened_at }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Opening amount</div>
            <div class="font-semibold">{{ number_format($session->opening_amount, 3) }} TND</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 flex items-center">
            <form method="POST" action="{{ route('cashier.sales.store') }}" class="w-full">
                @csrf
                <button class="w-full bg-sky-600 hover:bg-sky-700 text-white rounded py-3 text-lg">+ New sale</button>
            </form>
        </div>
    </div>

    <h2 class="font-semibold mb-2">Sales in this session</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr><th class="p-3">#</th><th class="p-3">Time</th><th class="p-3 text-right">Total</th><th class="p-3">Status</th></tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr class="border-t">
                        <td class="p-3"><a class="text-sky-600" href="{{ route('cashier.sales.show', $sale) }}">{{ $sale->id }}</a></td>
                        <td class="p-3">{{ $sale->sale_date }}</td>
                        <td class="p-3 text-right">{{ number_format($sale->total, 3) }}</td>
                        <td class="p-3">@include('partials.status-badge', ['status' => $sale->status])</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-3 text-gray-500">No sales yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-md">
        <h2 class="font-semibold mb-3">Close session</h2>
        <form method="POST" action="{{ route('cashier.sessions.close', $session) }}" class="space-y-4"
              onsubmit="return confirm('Close this session?')">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm mb-1">Counted cash in drawer (TND)</label>
                <input name="closing_amount" type="number" step="0.001" min="0" required class="w-full border rounded px-3 py-2">
            </div>
            <button class="bg-red-600 hover:bg-red-700 text-white rounded px-4 py-2">Close session</button>
        </form>
    </div>
@endif
@endsection

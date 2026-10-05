{{-- Usage: @include('partials.status-badge', ['status' => $sale->status]) --}}
@php
    $colors = [
        'PENDING'   => 'bg-yellow-100 text-yellow-800',
        'PAID'      => 'bg-green-100 text-green-800',
        'CANCELLED' => 'bg-red-100 text-red-800',
        'OPEN'      => 'bg-green-100 text-green-800',
        'CLOSED'    => 'bg-gray-200 text-gray-700',
    ];
@endphp
<span class="px-2 py-0.5 rounded text-xs font-medium {{ $colors[$status] ?? 'bg-gray-100' }}">{{ $status }}</span>

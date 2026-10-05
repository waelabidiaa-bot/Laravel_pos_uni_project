{{-- Expects: $products (paginator of Product with stock) --}}
@extends('layouts.app')
@section('title', 'Stock')

@section('content')
<h1 class="text-2xl font-bold mb-6">Stock</h1>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="p-3">Product</th><th class="p-3">Level</th><th class="p-3">Update</th></tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                @php $low = $product->stock && $product->stock->quantity <= $product->stock->min_threshold; @endphp
                <tr class="border-t {{ $low ? 'bg-red-50' : '' }}">
                    <td class="p-3">{{ $product->name }}</td>
                    <td class="p-3">
                        {{ $product->stock->quantity ?? 0 }}
                        @if ($low) <span class="ml-2 text-xs text-red-700 font-medium">LOW</span> @endif
                    </td>
                    <td class="p-3">
                        <form method="POST" action="{{ route('admin.stock.update', $product) }}" class="flex gap-2 items-center">
                            @csrf @method('PATCH')
                            <input name="quantity" type="number" min="0" value="{{ $product->stock->quantity ?? 0 }}" class="border rounded px-2 py-1 w-24">
                            <input name="min_threshold" type="number" min="0" value="{{ $product->stock->min_threshold ?? 0 }}" class="border rounded px-2 py-1 w-24" title="Low-stock threshold">
                            <button class="text-sky-600">Save</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection

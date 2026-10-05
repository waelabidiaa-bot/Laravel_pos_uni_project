{{-- Expects: $products (paginator of Product with category, stock) --}}
@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-sky-600 hover:bg-sky-700 text-white rounded px-4 py-2">New product</a>
</div>

<form method="GET" class="mb-4">
    <input name="q" value="{{ request('q') }}" placeholder="Search name or barcode" class="border rounded px-3 py-2 w-72">
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="p-3">Name</th><th class="p-3">Barcode</th><th class="p-3">Category</th>
                <th class="p-3 text-right">Price</th><th class="p-3 text-right">In stock</th><th class="p-3"></th></tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-t">
                    <td class="p-3">{{ $product->name }}</td>
                    <td class="p-3 text-gray-500">{{ $product->barcode }}</td>
                    <td class="p-3">{{ $product->category->name }}</td>
                    <td class="p-3 text-right">{{ number_format($product->price, 3) }}</td>
                    <td class="p-3 text-right">{{ $product->stock->quantity ?? 0 }}</td>
                    <td class="p-3 text-right flex gap-3 justify-end">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-sky-600">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                              onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-3 text-gray-500">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->withQueryString()->links() }}</div>
@endsection

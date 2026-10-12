@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-sky-600">INVENTORY MANAGEMENT</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
                Products
            </h1>
            <p class="mt-2 text-sm text-slate-500">
                Manage products, images, prices, profitability, and inventory.
            </p>
        </div>

        <a
            href="{{ route('admin.products.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
        >
            <i class="fa-solid fa-plus"></i>
            Add product
        </a>
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

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Matching products</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold text-slate-900">
                {{ number_format($products->total()) }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Products matching your filters</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Current page</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                    <i class="fa-solid fa-table-list"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold text-slate-900">
                {{ $products->count() }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Products currently displayed</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Categories</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold text-slate-900">
                {{ $categories->count() }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Available categories</p>
        </div>

    </div>

    {{-- Search and filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.products.index') }}">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">

                <div class="lg:col-span-5">
                    <label for="q" class="mb-2 block text-sm font-medium text-slate-700">
                        Search products
                    </label>

                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input
                            id="q"
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Product name or barcode..."
                            class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-3 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                        >
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Category
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                    >
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected((string) $categoryId === (string) $category->id)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="stock_status" class="mb-2 block text-sm font-medium text-slate-700">
                        Stock status
                    </label>

                    <select
                        id="stock_status"
                        name="stock_status"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                    >
                        <option value="all" @selected($stockStatus === 'all')>All stock</option>
                        <option value="available" @selected($stockStatus === 'available')>Available</option>
                        <option value="low" @selected($stockStatus === 'low')>Low stock</option>
                        <option value="out" @selected($stockStatus === 'out')>Out of stock</option>
                    </select>
                </div>

                <div class="flex items-end gap-2 lg:col-span-2">
                    <button
                        type="submit"
                        class="flex-1 rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-700"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admin.products.index') }}"
                        title="Clear filters"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-slate-600 hover:bg-slate-50"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- Product table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-slate-900">Product inventory</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Review inventory, pricing, and estimated profitability.
                </p>
            </div>

            <span class="text-sm text-slate-500">
                @if ($products->total() > 0)
                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }}
                    of {{ $products->total() }}
                @else
                    No products found
                @endif
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px] text-left text-sm">

                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Product</th>
                        <th class="px-5 py-4 font-semibold">Barcode</th>
                        <th class="px-5 py-4 font-semibold">Category</th>
                        <th class="px-5 py-4 text-right font-semibold">Purchase price</th>
                        <th class="px-5 py-4 text-right font-semibold">Selling price</th>
                        <th class="px-5 py-4 text-right font-semibold">Profit / unit</th>
                        <th class="px-5 py-4 text-right font-semibold">Margin</th>
                        <th class="px-5 py-4 text-right font-semibold">Quantity</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($products as $product)

                        @php
                            $quantity = (int) ($product->stock?->quantity ?? 0);
                            $threshold = (int) ($product->stock?->min_threshold ?? 0);

                            $purchasePrice = $product->purchase_price !== null
                                ? (float) $product->purchase_price
                                : null;

                            $sellingPrice = (float) $product->price;

                            $profit = $purchasePrice !== null
                                ? $sellingPrice - $purchasePrice
                                : null;

                            $margin = $purchasePrice !== null && $sellingPrice > 0
                                ? ($profit / $sellingPrice) * 100
                                : null;

                            if ($quantity <= 0) {
                                $stockLabel = 'Out of stock';
                                $stockClasses = 'bg-red-50 text-red-700 ring-red-600/20';
                            } elseif ($quantity <= $threshold) {
                                $stockLabel = 'Low stock';
                                $stockClasses = 'bg-amber-50 text-amber-700 ring-amber-600/20';
                            } else {
                                $stockLabel = 'In stock';
                                $stockClasses = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
                            }
                        @endphp

                        <tr class="transition hover:bg-slate-50/80">

                            {{-- Image and name --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">

                                    @if ($product->image)
                                        <img
                                            src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                            class="h-12 w-12 shrink-0 rounded-xl border border-slate-200 bg-white object-contain p-1"
                                        >
                                    @else
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                                            <i class="fa-solid fa-box text-lg"></i>
                                        </div>
                                    @endif

                                    <div>
                                        <p class="font-semibold text-slate-900">
                                            {{ $product->name }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            Product #{{ $product->id }}
                                        </p>
                                    </div>

                                </div>
                            </td>

                            {{-- Barcode --}}
                            <td class="px-5 py-4">
                                @if ($product->barcode)
                                    <span class="font-mono text-xs text-slate-600">
                                        {{ $product->barcode }}
                                    </span>
                                @else
                                    <span class="text-slate-400">No barcode</span>
                                @endif
                            </td>

                            {{-- Category --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                    {{ $product->category?->name ?? 'Uncategorized' }}
                                </span>
                            </td>

                            {{-- Purchase price --}}
                            <td class="px-5 py-4 text-right">
                                @if ($purchasePrice !== null)
                                    <span class="font-medium text-slate-700">
                                        {{ number_format($purchasePrice, 3) }}
                                    </span>
                                    <span class="text-xs text-slate-500">TND</span>
                                @else
                                    <span class="text-xs text-slate-400">Not set</span>
                                @endif
                            </td>

                            {{-- Selling price --}}
                            <td class="px-5 py-4 text-right">
                                <span class="font-semibold text-slate-900">
                                    {{ number_format($sellingPrice, 3) }}
                                </span>
                                <span class="text-xs text-slate-500">TND</span>
                            </td>

                            {{-- Profit per unit --}}
                            <td class="px-5 py-4 text-right">
                                @if ($profit !== null)
                                    <span class="font-semibold {{ $profit < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ number_format($profit, 3) }}
                                    </span>
                                    <span class="text-xs text-slate-500">TND</span>
                                @else
                                    <span class="text-xs text-slate-400">Unknown</span>
                                @endif
                            </td>

                            {{-- Margin --}}
                            <td class="px-5 py-4 text-right">
                                @if ($margin !== null)
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $margin < 0 ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ number_format($margin, 2) }}%
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Quantity --}}
                            <td class="px-5 py-4 text-right">
                                <span class="font-semibold text-slate-900">
                                    {{ $quantity }}
                                </span>
                                <p class="mt-1 text-xs text-slate-400">
                                    Minimum: {{ $threshold }}
                                </p>
                            </td>

                            {{-- Stock status --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $stockClasses }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $stockLabel }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">

                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                        title="Edit product"
                                        aria-label="Edit product"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.products.destroy', $product) }}"
                                        onsubmit="return confirm('Delete this product?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Delete product"
                                            aria-label="Delete product"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <i class="fa-solid fa-box-open text-2xl"></i>
                                </div>

                                <h3 class="mt-4 font-semibold text-slate-900">
                                    No products found
                                </h3>

                                <p class="mt-2 text-sm text-slate-500">
                                    Try changing your search or filters.
                                </p>

                                <a
                                    href="{{ route('admin.products.create') }}"
                                    class="mt-4 inline-flex items-center gap-2 font-semibold text-sky-600 hover:text-sky-700"
                                >
                                    <i class="fa-solid fa-plus"></i>
                                    Add your first product
                                </a>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $products->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
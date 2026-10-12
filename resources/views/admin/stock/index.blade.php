{{-- Expects: $products (paginator of Product with stock) --}}

@extends('layouts.app')

@section('title', 'Inventory')

@section('content')

@php
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';

    $lowStockCount = $products->getCollection()->filter(function ($product) {
        return $product->stock
            && $product->stock->quantity <= $product->stock->min_threshold;
    })->count();
@endphp

<div class="space-y-8">

    {{-- Page header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Inventory
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Monitor product quantities and manage stock levels.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 self-start rounded-full
                    bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700">

            <i class="fa-solid fa-boxes-stacked"></i>

            {{ $products->total() }} products

        </div>

    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200
                    bg-emerald-50 p-4 text-sm text-emerald-800">

            <i class="fa-solid fa-circle-check"></i>

            <span>{{ session('success') }}</span>

        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">

            <p class="font-semibold">
                Please correct the following errors:
            </p>

            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif

    {{-- Inventory summary --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        {{-- Total products --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total products
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $products->total() }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center
                            rounded-xl bg-sky-50 text-sky-600">

                    <i class="fa-solid fa-boxes-stacked text-xl"></i>

                </div>

            </div>

            <p class="mt-3 text-xs text-slate-500">
                Products registered in the inventory.
            </p>

        </div>

        {{-- Low stock --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Low-stock products
                    </p>

                    <p class="mt-2 text-3xl font-bold
                              {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-slate-800' }}">

                        {{ $lowStockCount }}

                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center
                            rounded-xl {{ $lowStockCount > 0
                                ? 'bg-rose-50 text-rose-600'
                                : 'bg-emerald-50 text-emerald-600' }}">

                    <i class="fa-solid {{ $lowStockCount > 0
                        ? 'fa-triangle-exclamation'
                        : 'fa-circle-check' }} text-xl"></i>

                </div>

            </div>

            <p class="mt-3 text-xs text-slate-500">
                Products at or below their minimum stock threshold on this page.
            </p>

        </div>

    </div>

    {{-- Inventory table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        {{-- Table header --}}
        <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5
                    sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Stock management
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Update quantities and configure low-stock thresholds.
                </p>
            </div>

            @if ($lowStockCount > 0)
                <span class="inline-flex items-center gap-2 self-start rounded-full
                             bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    {{ $lowStockCount }} low-stock
                </span>
            @endif

        </div>

        @if ($products->isEmpty())

            {{-- Empty state --}}
            <div class="px-6 py-14 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center
                            rounded-xl bg-slate-100">

                    <i class="fa-solid fa-box-open text-2xl text-slate-400"></i>

                </div>

                <h3 class="mt-4 font-semibold text-slate-800">
                    No products found
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Add products to your store to manage their inventory here.
                </p>

            </div>

        @else

            {{-- Responsive table --}}
            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Product
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Current stock
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Update inventory
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($products as $product)

                            @php
                                $quantity = $product->stock->quantity ?? 0;

                                $threshold = $product->stock->min_threshold ?? 0;

                                $low = $product->stock
                                    && $quantity <= $threshold;
                            @endphp

                            <tr class="transition hover:bg-slate-50
                                       {{ $low ? 'bg-rose-50/40' : '' }}">

                                {{-- Product name --}}
                                <td class="whitespace-nowrap px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center
                                                    justify-center rounded-lg
                                                    {{ $low
                                                        ? 'bg-rose-100 text-rose-600'
                                                        : 'bg-slate-100 text-slate-600' }}">

                                            <i class="fa-solid fa-box"></i>

                                        </div>

                                        <div>
                                            <p class="font-medium text-slate-800">
                                                {{ $product->name }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Product ID: {{ $product->id }}
                                            </p>
                                        </div>

                                    </div>

                                </td>

                                {{-- Current stock --}}
                                <td class="whitespace-nowrap px-6 py-4 text-center">

                                    <div class="flex flex-col items-center gap-2">

                                        <span class="text-lg font-bold
                                                     {{ $low
                                                        ? 'text-rose-600'
                                                        : 'text-slate-800' }}">

                                            {{ $quantity }}

                                        </span>

                                        @if ($low)

                                            <span class="inline-flex items-center gap-1
                                                         rounded-full bg-rose-100 px-2.5 py-1
                                                         text-xs font-semibold text-rose-700">

                                                <i class="fa-solid fa-triangle-exclamation"></i>

                                                Low stock

                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-1
                                                         rounded-full bg-emerald-50 px-2.5 py-1
                                                         text-xs font-medium text-emerald-700">

                                                <i class="fa-solid fa-check"></i>

                                                In stock

                                            </span>

                                        @endif

                                    </div>

                                </td>

                                {{-- Update stock --}}
                                <td class="px-6 py-4">

                                    <form
                                        method="POST"
                                        action="{{ route('admin.stock.update', $product) }}"
                                        class="flex min-w-[420px] flex-wrap items-end gap-3">

                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label
                                                for="quantity-{{ $product->id }}"
                                                class="mb-1.5 block text-xs font-medium text-slate-600">

                                                Quantity
                                            </label>

                                            <input
                                                id="quantity-{{ $product->id }}"
                                                name="quantity"
                                                type="number"
                                                min="0"
                                                step="1"
                                                required
                                                value="{{ $quantity }}"
                                                class="w-24 rounded-lg border border-slate-300
                                                       bg-white px-3 py-2 text-sm text-slate-800
                                                       focus:border-sky-500 focus:outline-none
                                                       focus:ring-2 focus:ring-sky-100">
                                        </div>

                                        <div>
                                            <label
                                                for="threshold-{{ $product->id }}"
                                                class="mb-1.5 block text-xs font-medium text-slate-600">

                                                Minimum stock
                                            </label>

                                            <input
                                                id="threshold-{{ $product->id }}"
                                                name="min_threshold"
                                                type="number"
                                                min="0"
                                                step="1"
                                                required
                                                value="{{ $threshold }}"
                                                title="Low-stock threshold"
                                                class="w-28 rounded-lg border border-slate-300
                                                       bg-white px-3 py-2 text-sm text-slate-800
                                                       focus:border-sky-500 focus:outline-none
                                                       focus:ring-2 focus:ring-sky-100">
                                        </div>

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center gap-2
                                                   rounded-lg bg-slate-900 px-4 py-2 text-sm
                                                   font-medium text-white transition
                                                   hover:bg-slate-800">

                                            <i class="fa-solid fa-floppy-disk"></i>

                                            Save

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

        {{-- Pagination footer --}}
        @if ($products->hasPages() || $products->total() > 0)

            <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50
                        px-6 py-4 sm:flex-row sm:items-center sm:justify-between">

                <p class="text-sm text-slate-500">

                    Showing
                    <span class="font-medium text-slate-700">
                        {{ $products->firstItem() ?? 0 }}
                    </span>

                    to

                    <span class="font-medium text-slate-700">
                        {{ $products->lastItem() ?? 0 }}
                    </span>

                    of

                    <span class="font-medium text-slate-700">
                        {{ $products->total() }}
                    </span>

                    products

                </p>

                <div>
                    {{ $products->links() }}
                </div>

            </div>

        @endif

    </div>

</div>

@endsection
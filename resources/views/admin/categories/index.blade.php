{{-- Expects: $categories (collection of Category with products_count) --}}

@extends('layouts.app')

@section('title', 'Categories')

@section('content')

@php
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';
@endphp

<div class="space-y-8">

    {{-- Page header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Categories
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Organize your products into categories and manage them easily.
            </p>
        </div>

        <span class="inline-flex items-center gap-2 self-start rounded-full
                     bg-sky-50 px-3 py-1.5 text-sm font-medium text-sky-700">
            <i class="fa-solid fa-layer-group"></i>
            {{ $categories->count() }} categories
        </span>
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

    {{-- Add category --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-5">
            <h2 class="text-lg font-semibold text-slate-800">
                Add a category
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Create a category to organize your store's products.
            </p>
        </div>

        <form method="POST"
              action="{{ route('admin.categories.store') }}"
              class="flex flex-col gap-3 sm:flex-row sm:items-start">

            @csrf

            <div class="flex-1">
                <label for="name"
                       class="mb-1.5 block text-sm font-medium text-slate-700">
                    Category name
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    placeholder="e.g. Beverages"
                    required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5
                           text-sm text-slate-800 placeholder:text-slate-400
                           focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                >

                @error('name')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg
                       bg-slate-900 px-5 py-2.5 text-sm font-medium text-white
                       transition hover:bg-slate-800 sm:mt-7">

                <i class="fa-solid fa-plus"></i>
                Add category
            </button>

        </form>
    </div>

    {{-- Categories table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5
                    sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    All categories
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    View, rename, and delete product categories.
                </p>
            </div>

            <span class="text-sm text-slate-500">
                {{ $categories->count() }} total
            </span>

        </div>

        @if ($categories->isEmpty())

            {{-- Empty state --}}
            <div class="px-6 py-14 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center
                            rounded-xl bg-slate-100">
                    <i class="fa-solid fa-layer-group text-2xl text-slate-400"></i>
                </div>

                <h3 class="mt-4 font-semibold text-slate-800">
                    No categories yet
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Create your first category using the form above.
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
                                Category
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Products
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($categories as $category)

                            <tr class="transition hover:bg-slate-50">

                                {{-- Category name and rename form --}}
                                <td class="px-6 py-4">

                                    <form
                                        method="POST"
                                        action="{{ route('admin.categories.update', $category) }}"
                                        class="flex flex-col gap-2 sm:flex-row sm:items-center">

                                        @csrf
                                        @method('PUT')

                                        <div class="flex-1">
                                            <label
                                                for="category-{{ $category->id }}"
                                                class="sr-only">
                                                Category name
                                            </label>

                                            <input
                                                id="category-{{ $category->id }}"
                                                name="name"
                                                type="text"
                                                value="{{ $category->name }}"
                                                required
                                                class="w-full rounded-lg border border-slate-200
                                                       bg-white px-3 py-2 text-sm text-slate-800
                                                       focus:border-sky-500 focus:outline-none
                                                       focus:ring-2 focus:ring-sky-100"
                                            >
                                        </div>

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center gap-2
                                                   rounded-lg border border-slate-200 bg-white
                                                   px-3 py-2 text-sm font-medium text-slate-700
                                                   transition hover:bg-slate-100">

                                            <i class="fa-solid fa-pen-to-square"></i>
                                            Rename
                                        </button>

                                    </form>

                                </td>

                                {{-- Number of products --}}
                                <td class="whitespace-nowrap px-6 py-4 text-center">

                                    <span class="inline-flex items-center rounded-full
                                                 bg-slate-100 px-3 py-1 text-sm font-medium
                                                 text-slate-700">

                                        {{ $category->products_count }}

                                    </span>

                                </td>

                                {{-- Delete action --}}
                                <td class="whitespace-nowrap px-6 py-4 text-right">

                                    <form
                                        method="POST"
                                        action="{{ route('admin.categories.destroy', $category) }}"
                                        onsubmit="return confirm('Delete this category?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center gap-2
                                                   rounded-lg border border-rose-200 bg-white
                                                   px-3 py-2 text-sm font-medium text-rose-600
                                                   transition hover:bg-rose-50">

                                            <i class="fa-solid fa-trash"></i>
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

        {{-- Table footer --}}
        @if ($categories->isNotEmpty())
            <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">
                <p class="text-sm text-slate-500">
                    Showing {{ $categories->count() }} categories.
                </p>
            </div>
        @endif

    </div>

</div>

@endsection
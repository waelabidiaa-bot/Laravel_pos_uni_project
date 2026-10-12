{{-- Expects: $sales (paginator of Sale with cashSession.cashier) --}}

@extends('layouts.app')

@section('title', 'Sales')

@section('content')

@php
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';
@endphp

<div class="space-y-8">

    {{-- Page header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Sales
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Review transactions, monitor payment statuses, and inspect sale details.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 self-start rounded-full
                    bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700">

            <i class="fa-solid fa-receipt"></i>

            {{ $sales->total() }} sales

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

    {{-- Filters --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-5">
            <h2 class="text-lg font-semibold text-slate-800">
                Filter sales
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Narrow the results by date range or payment status.
            </p>
        </div>

        <form method="GET"
              action="{{ route('admin.sales.index') }}"
              class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Start date --}}
            <div>
                <label for="from"
                       class="mb-1.5 block text-sm font-medium text-slate-700">
                    From date
                </label>

                <input
                    id="from"
                    type="date"
                    name="from"
                    value="{{ request('from') }}"
                    class="w-full rounded-lg border border-slate-300 bg-white
                           px-3 py-2.5 text-sm text-slate-800
                           focus:border-sky-500 focus:outline-none
                           focus:ring-2 focus:ring-sky-100">
            </div>

            {{-- End date --}}
            <div>
                <label for="to"
                       class="mb-1.5 block text-sm font-medium text-slate-700">
                    To date
                </label>

                <input
                    id="to"
                    type="date"
                    name="to"
                    value="{{ request('to') }}"
                    class="w-full rounded-lg border border-slate-300 bg-white
                           px-3 py-2.5 text-sm text-slate-800
                           focus:border-sky-500 focus:outline-none
                           focus:ring-2 focus:ring-sky-100">
            </div>

            {{-- Status --}}
            <div>
                <label for="status"
                       class="mb-1.5 block text-sm font-medium text-slate-700">
                    Sale status
                </label>

                <select
                    id="status"
                    name="status"
                    class="w-full rounded-lg border border-slate-300 bg-white
                           px-3 py-2.5 text-sm text-slate-800
                           focus:border-sky-500 focus:outline-none
                           focus:ring-2 focus:ring-sky-100">

                    <option value="">All statuses</option>

                    @foreach (['PENDING', 'PAID', 'CANCELLED'] as $s)
                        <option value="{{ $s }}"
                                @selected(request('status') === $s)>
                            {{ $s }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="inline-flex flex-1 items-center justify-center gap-2
                           rounded-lg bg-slate-900 px-4 py-2.5 text-sm
                           font-medium text-white transition hover:bg-slate-800">

                    <i class="fa-solid fa-filter"></i>

                    Apply filters
                </button>

                <a
                    href="{{ route('admin.sales.index') }}"
                    class="inline-flex items-center justify-center gap-2
                           rounded-lg border border-slate-200 bg-white
                           px-4 py-2.5 text-sm font-medium text-slate-700
                           transition hover:bg-slate-50"
                    title="Clear filters">

                    <i class="fa-solid fa-rotate-left"></i>

                    <span class="hidden xl:inline">Reset</span>

                </a>

            </div>

        </form>

    </div>

    {{-- Sales table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        {{-- Table header --}}
        <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5
                    sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Sales history
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    View transaction information and open individual sales.
                </p>
            </div>

            <span class="text-sm text-slate-500">
                {{ $sales->total() }} results
            </span>

        </div>

        @if ($sales->isEmpty())

            {{-- Empty state --}}
            <div class="px-6 py-14 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center
                            rounded-xl bg-slate-100">

                    <i class="fa-solid fa-receipt text-2xl text-slate-400"></i>

                </div>

                <h3 class="mt-4 font-semibold text-slate-800">
                    No sales found
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Try changing your filters or clearing them to see more sales.
                </p>

                @if (request()->hasAny(['from', 'to', 'status']))
                    <a
                        href="{{ route('admin.sales.index') }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg
                               bg-slate-900 px-4 py-2 text-sm font-medium
                               text-white transition hover:bg-slate-800">

                        <i class="fa-solid fa-rotate-left"></i>

                        Clear filters
                    </a>
                @endif

            </div>

        @else

            {{-- Responsive table --}}
            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Sale ID
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Cashier
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Date
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Total
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Details
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($sales as $sale)

                            <tr class="transition hover:bg-slate-50">

                                {{-- Sale ID --}}
                                <td class="whitespace-nowrap px-6 py-4">

                                    <a
                                        href="{{ route('admin.sales.show', $sale) }}"
                                        class="inline-flex items-center gap-2 font-semibold
                                               text-sky-700 transition hover:text-sky-900">

                                        <i class="fa-solid fa-receipt text-slate-400"></i>

                                        #{{ $sale->id }}

                                    </a>

                                </td>

                                {{-- Cashier --}}
                                <td class="whitespace-nowrap px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-9 w-9 items-center justify-center
                                                    rounded-full bg-slate-100 text-slate-600">

                                            <i class="fa-solid fa-user"></i>

                                        </div>

                                        <span class="text-sm font-medium text-slate-700">

                                            {{ $sale->cashSession?->cashier?->name ?? 'Unknown cashier' }}

                                        </span>

                                    </div>

                                </td>

                                {{-- Date --}}
                                <td class="whitespace-nowrap px-6 py-4">

                                    <div class="flex items-center gap-2 text-sm text-slate-600">

                                        <i class="fa-regular fa-calendar text-slate-400"></i>

                                        {{ $sale->sale_date }}

                                    </div>

                                </td>

                                {{-- Total --}}
                                <td class="whitespace-nowrap px-6 py-4 text-right">

                                    <span class="font-semibold text-slate-800">
                                        {{ number_format((float) $sale->total, 3) }}
                                    </span>

                                    <span class="ml-1 text-xs font-medium text-slate-500">
                                        {{ $currency }}
                                    </span>

                                </td>

                                {{-- Status --}}
                                <td class="whitespace-nowrap px-6 py-4 text-center">

                                    @include('partials.status-badge', [
                                        'status' => $sale->status
                                    ])

                                </td>

                                {{-- Details link --}}
                                <td class="whitespace-nowrap px-6 py-4 text-right">

                                    <a
                                        href="{{ route('admin.sales.show', $sale) }}"
                                        class="inline-flex items-center gap-2 rounded-lg
                                               border border-slate-200 bg-white px-3 py-2
                                               text-sm font-medium text-slate-700
                                               transition hover:bg-slate-100">

                                        View

                                        <i class="fa-solid fa-arrow-right"></i>

                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50
                        px-6 py-4 sm:flex-row sm:items-center sm:justify-between">

                <p class="text-sm text-slate-500">

                    Showing
                    <span class="font-medium text-slate-700">
                        {{ $sales->firstItem() ?? 0 }}
                    </span>

                    to

                    <span class="font-medium text-slate-700">
                        {{ $sales->lastItem() ?? 0 }}
                    </span>

                    of

                    <span class="font-medium text-slate-700">
                        {{ $sales->total() }}
                    </span>

                    sales

                </p>

                <div>
                    {{ $sales->withQueryString()->links() }}
                </div>

            </div>

        @endif

    </div>

</div>

@endsection
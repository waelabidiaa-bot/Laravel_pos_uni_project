{{-- Expects: $sale (with items.product, payments, cashSession.cashier) --}}

@extends('layouts.app')

@section('title', 'Sale #' . $sale->id)

@section('content')

@php
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';

    $cashierName = $sale->cashSession?->cashier?->name ?? 'Unknown cashier';
@endphp

<div class="space-y-8">

    {{-- Back navigation --}}
    <div>
        <a
            href="{{ route('admin.sales.index') }}"
            class="inline-flex items-center gap-2 text-sm font-medium
                   text-slate-500 transition hover:text-slate-900">

            <i class="fa-solid fa-arrow-left"></i>

            Back to sales
        </a>
    </div>

    {{-- Page header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>
            <div class="flex flex-wrap items-center gap-3">

                <h1 class="text-2xl font-bold text-slate-800">
                    Sale #{{ $sale->id }}
                </h1>

                @include('partials.status-badge', [
                    'status' => $sale->status
                ])

            </div>

            <p class="mt-2 text-sm text-slate-500">
                Review the transaction, cashier information, products, and payment details.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 self-start rounded-lg
                    border border-slate-200 bg-white px-4 py-2.5
                    text-sm font-medium text-slate-700 shadow-sm">

            <i class="fa-solid fa-receipt text-slate-400"></i>

            Transaction #{{ $sale->id }}

        </div>

    </div>

    {{-- Sale information cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        {{-- Cashier information --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center
                            rounded-xl bg-sky-50 text-sky-600">

                    <i class="fa-solid fa-user text-xl"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-sm font-medium text-slate-500">
                        Cashier
                    </p>

                    <p class="mt-1 break-words text-lg font-semibold text-slate-800">
                        {{ $cashierName }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        Responsible for this transaction
                    </p>

                </div>

            </div>

        </div>

        {{-- Sale date --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center
                            rounded-xl bg-violet-50 text-violet-600">

                    <i class="fa-solid fa-calendar-days text-xl"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-sm font-medium text-slate-500">
                        Sale date
                    </p>

                    <p class="mt-1 break-words text-lg font-semibold text-slate-800">
                        {{ $sale->sale_date }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        Transaction date recorded by the system
                    </p>

                </div>

            </div>

        </div>

    </div>

    {{-- Existing sale summary --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h2 class="text-lg font-semibold text-slate-800">
                Transaction details
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Products, quantities, totals, and payment information.
            </p>

        </div>

        <div class="p-4 sm:p-6">

            @include('partials.sale-summary', [
                'sale' => $sale,
                'currency' => $currency
            ])

        </div>

    </div>

    {{-- Footer --}}
    <div class="flex flex-col gap-3 border-t border-slate-200 pt-5
                sm:flex-row sm:items-center sm:justify-between">

        <p class="text-xs text-slate-500">
            Sale reference: #{{ $sale->id }}
        </p>

        <a
            href="{{ route('admin.sales.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg
                   border border-slate-200 bg-white px-4 py-2.5
                   text-sm font-medium text-slate-700 transition
                   hover:bg-slate-50">

            <i class="fa-solid fa-arrow-left"></i>

            Return to sales
        </a>

    </div>

</div>

@endsection
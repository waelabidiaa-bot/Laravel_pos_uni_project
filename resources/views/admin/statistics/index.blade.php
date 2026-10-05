


@extends('layouts.app')

@section('title', 'Statistics')

@section('content')

<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Sales Statistics
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Overview of your store's sales performance.
        </p>
    </div>


    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Revenue --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm
                    hover:shadow-md transition-shadow">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Revenue
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ number_format($totalRevenue, 3) }}
                        <span class="text-sm font-medium text-slate-400">
                            TND
                        </span>
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-lg bg-emerald-50
                            flex items-center justify-center">

                    <i class="fa-solid fa-coins text-emerald-600"></i>

                </div>

            </div>

            <div class="mt-4 text-xs text-slate-400">
                Total revenue generated
            </div>

        </div>


        {{-- Total Sales --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm
                    hover:shadow-md transition-shadow">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Sales
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ number_format($totalSales) }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-lg bg-sky-50
                            flex items-center justify-center">

                    <i class="fa-solid fa-receipt text-sky-600"></i>

                </div>

            </div>

            <div class="mt-4 text-xs text-slate-400">
                Completed transactions
            </div>

        </div>


        {{-- Products Sold --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm
                    hover:shadow-md transition-shadow">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Products Sold
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ number_format($productsSold) }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-lg bg-violet-50
                            flex items-center justify-center">

                    <i class="fa-solid fa-box-open text-violet-600"></i>

                </div>

            </div>

            <div class="mt-4 text-xs text-slate-400">
                Items sold to customers
            </div>

        </div>


        {{-- Average Sale --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm
                    hover:shadow-md transition-shadow">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Average Sale
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ number_format($averageSale, 3) }}
                        <span class="text-sm font-medium text-slate-400">
                            TND
                        </span>
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-lg bg-amber-50
                            flex items-center justify-center">

                    <i class="fa-solid fa-chart-line text-amber-600"></i>

                </div>

            </div>

            <div class="mt-4 text-xs text-slate-400">
                Average transaction value
            </div>

        </div>

    </div>


    {{-- Overview --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-semibold text-slate-800">
                Performance Overview
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Summary of your current sales activity.
            </p>

        </div>

        <div class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>
                    <p class="text-sm text-slate-500">
                        Sales Activity
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-800">
                        {{ number_format($totalSales) }}
                        <span class="text-sm font-normal text-slate-400">
                            transactions
                        </span>
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">
                        Revenue Generated
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-800">
                        {{ number_format($totalRevenue, 3) }}
                        <span class="text-sm font-normal text-slate-400">
                            TND
                        </span>
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">
                        Items Sold
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-800">
                        {{ number_format($productsSold) }}
                        <span class="text-sm font-normal text-slate-400">
                            products
                        </span>
                    </p>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
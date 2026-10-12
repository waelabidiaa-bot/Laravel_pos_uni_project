@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Overview')

@section('content')

<div class="space-y-8">

    {{-- Welcome section --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 18 ? 'afternoon' : 'evening') }},
                {{ auth()->user()->name ?? 'Admin' }}
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Here's what's happening with your store today.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm sm:self-auto">
            <i class="fa-regular fa-calendar text-blue-600"></i>

            <span class="text-sm font-medium text-slate-700">
                {{ now()->format('D, d M Y') }}
            </span>
        </div>
    </div>


    {{-- KPI cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Sales today --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Sales Today
                    </p>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($stats['sales_today']) }}
                    </h2>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <i class="fa-solid fa-receipt text-xl"></i>
                </div>
            </div>

            <p class="mt-4 text-xs text-slate-500">
                <i class="fa-solid fa-circle-check mr-1 text-emerald-500"></i>
                Paid transactions today
            </p>
        </div>


        {{-- Revenue today --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Revenue Today
                    </p>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($stats['revenue_today'], 3) }}
                    </h2>

                    <p class="mt-1 text-xs font-medium text-slate-500">
                        Tunisian Dinar (TND)
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
            </div>

            <p class="mt-4 text-xs text-slate-500">
                <i class="fa-solid fa-chart-line mr-1 text-emerald-500"></i>
                From paid sales today
            </p>
        </div>


        {{-- Low stock --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Low-Stock Products
                    </p>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($stats['low_stock']) }}
                    </h2>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-boxes-stacked text-xl"></i>
                </div>
            </div>

            <p class="mt-4 text-xs text-slate-500">
                @if ($stats['low_stock'] > 0)
                    <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-500"></i>
                    Restocking may be required
                @else
                    <i class="fa-solid fa-circle-check mr-1 text-emerald-500"></i>
                    All stock levels look good
                @endif
            </p>
        </div>


        {{-- Cashiers --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Registered Cashiers
                    </p>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($stats['cashiers']) }}
                    </h2>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
            </div>

            <p class="mt-4 text-xs text-slate-500">
                <i class="fa-solid fa-user-check mr-1 text-violet-500"></i>
                Users with the cashier role
            </p>
        </div>

    </div>


    {{-- Sales chart --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

        <div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Sales Overview
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Daily revenue from the last seven days.
                </p>
            </div>

            <div class="flex items-center gap-2 self-start rounded-lg bg-blue-50 px-3 py-2">
                <span class="h-2 w-2 rounded-full bg-blue-600"></span>

                <span class="text-xs font-semibold text-blue-700">
                    Revenue (TND)
                </span>
            </div>
        </div>

        <div class="relative h-64 sm:h-80">
            <canvas id="salesChart"></canvas>
        </div>

    </div>


    {{-- Recent sales --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col justify-between gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Recent Transactions
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    The latest 10 sales recorded in your POS.
                </p>
            </div>

            @if (Route::has('admin.sales.index'))
                <a
                    href="{{ route('admin.sales.index') }}"
                    class="inline-flex items-center justify-center gap-2 self-start rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 sm:self-auto"
                >
                    View all sales
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            @endif
        </div>


        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-left text-sm">

                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Sale ID</th>
                        <th class="px-6 py-4 font-semibold">Cashier</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 text-right font-semibold">Total</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($latestSales as $sale)

                        <tr class="transition hover:bg-slate-50">

                            <td class="px-6 py-4">
                                @if (Route::has('admin.sales.show'))
                                    <a
                                        href="{{ route('admin.sales.show', $sale) }}"
                                        class="font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        #{{ $sale->id }}
                                    </a>
                                @else
                                    <span class="font-semibold text-slate-700">
                                        #{{ $sale->id }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 font-semibold text-slate-600">
                                        {{ strtoupper(substr($sale->cashSession?->cashier?->name ?? 'U', 0, 1)) }}
                                    </div>

                                    <span class="font-medium text-slate-700">
                                        {{ $sale->cashSession?->cashier?->name ?? 'Unknown cashier' }}
                                    </span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-slate-500">
                                {{ $sale->sale_date
                                    ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y, H:i')
                                    : '—'
                                }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-slate-900">
                                {{ number_format($sale->total, 3) }}
                                <span class="text-xs font-normal text-slate-500">TND</span>
                            </td>

                            <td class="px-6 py-4">
                                @include('partials.status-badge', [
                                    'status' => $sale->status
                                ])
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">

                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <i class="fa-solid fa-receipt text-2xl"></i>
                                </div>

                                <h3 class="mt-4 font-semibold text-slate-800">
                                    No transactions yet
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Completed sales will appear here.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

    </div>


    {{-- Low-stock products --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Inventory Alerts
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Products at or below their minimum stock threshold.
                </p>
            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>


        <div class="divide-y divide-slate-100">

            @forelse ($lowStockProducts as $stock)

                <div class="flex flex-col justify-between gap-3 px-5 py-4 sm:flex-row sm:items-center sm:px-6">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                            <i class="fa-solid fa-box"></i>
                        </div>

                        <div>
                            <p class="font-semibold text-slate-800">
                                {{ $stock->product?->name ?? 'Product #' . $stock->product_id }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Product ID: {{ $stock->product_id }}
                            </p>
                        </div>

                    </div>


                    <div class="flex items-center gap-4 sm:justify-end">

                        <div class="text-left sm:text-right">
                            <p class="text-xs text-slate-500">
                                Current quantity
                            </p>

                            <p class="mt-1 font-bold {{ $stock->quantity <= 0 ? 'text-red-600' : 'text-amber-600' }}">
                                {{ $stock->quantity }}
                            </p>
                        </div>

                        <div class="text-left sm:text-right">
                            <p class="text-xs text-slate-500">
                                Minimum
                            </p>

                            <p class="mt-1 font-semibold text-slate-700">
                                {{ $stock->min_threshold }}
                            </p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $stock->quantity <= 0 ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $stock->quantity <= 0 ? 'Out of stock' : 'Low stock' }}
                        </span>

                    </div>

                </div>

            @empty

                <div class="px-6 py-12 text-center">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>

                    <h3 class="mt-3 font-semibold text-slate-800">
                        No stock alerts
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        No products are currently below their minimum stock threshold.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    {{-- Quick actions --}}
    <div>
        <h2 class="mb-4 text-lg font-bold text-slate-900">
            Quick Actions
        </h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            @if (Route::has('admin.products.index'))
                <a
                    href="{{ route('admin.products.index') }}"
                    class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white">
                        <i class="fa-solid fa-box-open text-lg"></i>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">Products</p>
                        <p class="mt-1 text-xs text-slate-500">Manage your catalog</p>
                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-xs text-slate-400"></i>
                </a>
            @endif


            @if (Route::has('admin.stock.index'))
                <a
                    href="{{ route('admin.stock.index') }}"
                    class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition group-hover:bg-amber-500 group-hover:text-white">
                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">Inventory</p>
                        <p class="mt-1 text-xs text-slate-500">Check stock levels</p>
                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-xs text-slate-400"></i>
                </a>
            @endif


            @if (Route::has('admin.statistics.index'))
                <a
                    href="{{ route('admin.statistics.index') }}"
                    class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-600 group-hover:text-white">
                        <i class="fa-solid fa-chart-pie text-lg"></i>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">Statistics</p>
                        <p class="mt-1 text-xs text-slate-500">Analyze store performance</p>
                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-xs text-slate-400"></i>
                </a>
            @endif


            @if (Route::has('admin.cashiers.index'))
                <a
                    href="{{ route('admin.cashiers.index') }}"
                    class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-violet-200 hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600 transition group-hover:bg-violet-600 group-hover:text-white">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">Cashiers</p>
                        <p class="mt-1 text-xs text-slate-500">Manage cashier accounts</p>
                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-xs text-slate-400"></i>
                </a>
            @endif

        </div>
    </div>

</div>


{{-- Sales chart initialization --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('salesChart');

        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        const labels = {{ \Illuminate\Support\Js::from($salesChart['labels']) }};
        const revenue = {{ \Illuminate\Support\Js::from($salesChart['revenue']) }};

        new Chart(canvas, {
            type: 'line',

            data: {
                labels: labels,

                datasets: [{
                    label: 'Revenue (TND)',
                    data: revenue,

                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.10)',

                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,

                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index',
                },

                plugins: {
                    legend: {
                        display: false,
                    },

                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,

                        callbacks: {
                            label: function (context) {
                                return 'Revenue: ' +
                                    Number(context.parsed.y).toLocaleString(
                                        'en-US',
                                        {
                                            minimumFractionDigits: 3,
                                            maximumFractionDigits: 3
                                        }
                                    ) + ' TND';
                            }
                        }
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false,
                        },

                        border: {
                            display: false,
                        },

                        ticks: {
                            color: '#64748b',
                        }
                    },

                    y: {
                        beginAtZero: true,

                        border: {
                            display: false,
                            dash: [4, 4],
                        },

                        grid: {
                            color: '#f1f5f9',
                        },

                        ticks: {
                            color: '#64748b',

                            callback: function (value) {
                                return Number(value).toLocaleString() + ' TND';
                            }
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
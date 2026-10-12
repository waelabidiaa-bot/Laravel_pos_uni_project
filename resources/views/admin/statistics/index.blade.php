@extends('layouts.app')

@section('title', 'Statistics')

@section('content')

@php
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';
@endphp
<div class="space-y-8">

    {{-- Page header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Financial Statistics
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Monitor revenue, costs, profitability, and product performance.
            </p>
        </div>

        <span class="inline-flex items-center gap-2 self-start rounded-full
                     bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700">
            <i class="fa-solid fa-circle-check"></i>
            Paid sales only
        </span>
    </div>

    {{-- Date filters --}}
    <form method="GET"
          action="{{ route('admin.statistics.index') }}"
          class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

        <div class="mb-4">
            <h2 class="font-semibold text-slate-800">Filter by date</h2>
            <p class="mt-1 text-sm text-slate-500">
                Leave both dates empty to view all paid sales.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
            <div>
                <label for="from" class="mb-1 block text-sm font-medium text-slate-700">
                    From
                </label>
                <input
                    id="from"
                    type="date"
                    name="from"
                    value="{{ $from }}"
                    max="{{ $to ?: now()->toDateString() }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm
                           focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
            </div>

            <div>
                <label for="to" class="mb-1 block text-sm font-medium text-slate-700">
                    To
                </label>
                <input
                    id="to"
                    type="date"
                    name="to"
                    value="{{ $to }}"
                    min="{{ $from }}"
                    max="{{ now()->toDateString() }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm
                           focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm
                               font-medium text-white hover:bg-slate-800">
                    <i class="fa-solid fa-filter mr-1"></i>
                    Apply
                </button>

                <a href="{{ route('admin.statistics.index') }}"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm
                          font-medium text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Revenue and sales cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Revenue --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Revenue</p>
                    <p class="mt-3 text-2xl font-bold text-slate-900">
                        {{ number_format($totalRevenue, 3) }}
                        <span class="text-sm font-medium text-slate-400">{{ $currency }}</span>
                    </p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-sky-50">
                    <i class="fa-solid fa-coins text-sky-600"></i>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Revenue from paid sales</p>
        </div>

        {{-- Cost --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Product Costs</p>
                    <p class="mt-3 text-2xl font-bold text-slate-900">
                        {{ number_format($totalCost, 3) }}
                        <span class="text-sm font-medium text-slate-400">{{ $currency }}</span>
                    </p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-50">
                    <i class="fa-solid fa-boxes-stacked text-amber-600"></i>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Estimated cost of goods sold</p>
        </div>

        {{-- Gross profit --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Gross Profit</p>
                    <p class="mt-3 text-2xl font-bold {{ $grossProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ number_format($grossProfit, 3) }}
                        <span class="text-sm font-medium text-slate-400">{{ $currency }}</span>
                    </p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50">
                    <i class="fa-solid fa-chart-line text-emerald-600"></i>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Revenue minus product costs</p>
        </div>

        {{-- Profit margin --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Profit Margin</p>
                    <p class="mt-3 text-2xl font-bold text-slate-900">
                        {{ number_format($profitMargin, 2) }}%
                    </p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-violet-50">
                    <i class="fa-solid fa-percent text-violet-600"></i>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">Gross profit as a share of revenue</p>
        </div>
    </div>

    {{-- Transaction statistics --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Completed Sales</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">
                {{ number_format($totalSales) }}
            </p>
            <p class="mt-2 text-xs text-slate-500">Fully paid transactions</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Products Sold</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">
                {{ number_format($productsSold) }}
            </p>
            <p class="mt-2 text-xs text-slate-500">Total units sold</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Average Sale</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">
                {{ number_format($averageSale, 3) }}
                <span class="text-sm font-medium text-slate-400">{{ $currency }}</span>
            </p>
            <p class="mt-2 text-xs text-slate-500">Revenue per paid transaction</p>
        </div>
    </div>

    {{-- Top-selling products --}}

    {{-- Financial Charts --}}
<div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

    {{-- Revenue Chart --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-800">
                Revenue Over Time
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Daily revenue from completed sales.
            </p>
        </div>

        <div class="relative h-72">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Profit Chart --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-800">
                Gross Profit Over Time
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Daily revenue minus product costs.
            </p>
        </div>

        <div class="relative h-72">
            <canvas id="profitChart"></canvas>
        </div>
    </div>

</div>

{{-- Best-Selling Products Chart --}}
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-800">
            Best-Selling Products
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Compare products by the number of units sold.
        </p>
    </div>

    @if ($topSellingProducts->isNotEmpty())
        <div class="relative h-80">
            <canvas id="productsChart"></canvas>
        </div>
    @else
        <p class="py-8 text-center text-sm text-slate-500">
            No paid sales are available for this period.
        </p>
    @endif
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dailyData = @json($dailyPerformance);

    const dates = dailyData.map(item => item.date);
    const revenue = dailyData.map(item => Number(item.revenue));
    const profit = dailyData.map(item => Number(item.profit));

    const currencyTooltip = {
        callbacks: {
            label: function (context) {
                return context.dataset.label + ': ' +
                    Number(context.raw).toFixed(3) + ' {{ $currency }}';
            }
        }
    };

    // Revenue chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Revenue',
                data: revenue,
                borderColor: '#0284c7',
                backgroundColor: 'rgba(2, 132, 199, 0.12)',
                fill: true,
                tension: 0.3,
                borderWidth: 2,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: currencyTooltip
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value + ' {{ $currency }}'
                    }
                }
            }
        }
    });

    // Gross profit chart
    new Chart(document.getElementById('profitChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Gross profit',
                data: profit,
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.12)',
                fill: true,
                tension: 0.3,
                borderWidth: 2,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: currencyTooltip
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value + ' {{ $currency }}'
                    }
                }
            }
        }
    });

    // Best-selling products chart
    const productCanvas = document.getElementById('productsChart');

    if (productCanvas) {
        const products = @json($topSellingProducts);

        new Chart(productCanvas, {
            type: 'bar',
            data: {
                labels: products.map(product => product.name),
                datasets: [{
                    label: 'Units sold',
                    data: products.map(product =>
                        Number(product.quantity_sold)
                    ),
                    backgroundColor: '#6366f1',
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: context =>
                                context.raw + ' units sold'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
});
</script>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-800">
                Top-Selling Products
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Your five best-selling products by units sold.
            </p>
        </div>

        

        @if ($topSellingProducts->isEmpty())
            <div class="px-6 py-12 text-center">
                <i class="fa-solid fa-chart-column text-3xl text-slate-300"></i>
                <p class="mt-3 font-medium text-slate-700">No paid sales found</p>
                <p class="mt-1 text-sm text-slate-500">
                    Try another date range or complete a sale first.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Product
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Units Sold
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Revenue
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($topSellingProducts as $product)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($product->image)
                                            <img
                                                src="{{ asset('storage/' . $product->image) }}"
                                                alt="{{ $product->name }}"
                                                class="h-10 w-10 rounded-lg border border-slate-200 object-cover">
                                        @else
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100">
                                                <i class="fa-solid fa-box text-slate-400"></i>
                                            </div>
                                        @endif

                                        <span class="font-medium text-slate-800">
                                            {{ $product->name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-slate-800">
                                    {{ number_format($product->quantity_sold) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-slate-700">
                                    {{ number_format($product->revenue, 3) }} {{ $currency }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Calculation note --}}
    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
        <div class="flex gap-3">
            <i class="fa-solid fa-circle-info mt-0.5 text-amber-600"></i>
            <div>
                <p class="text-sm font-semibold text-amber-900">
                    Important: how profit is calculated
                </p>
                <p class="mt-1 text-sm text-amber-800">
            New sale items use the purchase price recorded when they were added
            to a sale. Older sale items without a recorded purchase price use
            the current product purchase price as an estimate. Gross profit does
            not deduct rent, salaries, taxes, or other operating expenses.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
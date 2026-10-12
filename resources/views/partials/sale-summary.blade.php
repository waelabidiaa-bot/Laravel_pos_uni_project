{{-- Usage: @include('partials.sale-summary', ['sale' => $sale, 'currency' => $currency]) --}}

@php
    $currency = $currency ?? (\App\Models\Parametre::first()?->currency ?? 'TND');
@endphp

<div class="space-y-8">

    {{-- Products --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">

        <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="font-semibold text-slate-800">
                Products sold
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Items included in this transaction.
            </p>
        </div>

        @if ($sale->items->isEmpty())

            <div class="px-5 py-10 text-center">
                <i class="fa-solid fa-box-open text-2xl text-slate-400"></i>

                <p class="mt-3 text-sm text-slate-500">
                    No products found for this sale.
                </p>
            </div>

        @else

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200 text-sm">

                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Product
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Quantity
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Unit price
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Subtotal
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($sale->items as $item)

                            <tr class="transition hover:bg-slate-50">

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="font-medium text-slate-800">
                                        {{ $item->product?->name ?? 'Deleted product' }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right
                                           text-slate-600">
                                    {{ $item->quantity }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right
                                           text-slate-600">
                                    {{ number_format((float) $item->unit_price, 3) }}
                                    <span class="ml-1 text-xs text-slate-400">
                                        {{ $currency }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right
                                           font-medium text-slate-800">
                                    {{ number_format((float) ($item->quantity * $item->unit_price), 3) }}
                                    <span class="ml-1 text-xs text-slate-400">
                                        {{ $currency }}
                                    </span>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                    <tfoot class="border-t border-slate-200 bg-slate-50">

                        <tr>
                            <td colspan="3"
                                class="px-5 py-4 text-right font-semibold text-slate-700">

                                Sale total

                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right
                                       text-lg font-bold text-slate-900">

                                {{ number_format((float) $sale->total, 3) }}

                                <span class="ml-1 text-sm font-medium text-slate-500">
                                    {{ $currency }}
                                </span>

                            </td>
                        </tr>

                    </tfoot>

                </table>

            </div>

        @endif

    </div>

    {{-- Payments --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">

        <div class="border-b border-slate-200 px-5 py-4">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center
                            rounded-lg bg-emerald-50 text-emerald-600">

                    <i class="fa-solid fa-credit-card"></i>

                </div>

                <div>
                    <h3 class="font-semibold text-slate-800">
                        Payments
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Payment methods and recorded amounts.
                    </p>
                </div>

            </div>

        </div>

        @if ($sale->payments->isEmpty())

            <div class="flex items-center gap-3 px-5 py-6">

                <i class="fa-solid fa-circle-info text-slate-400"></i>

                <p class="text-sm text-slate-500">
                    No payments recorded for this sale yet.
                </p>

            </div>

        @else

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200 text-sm">

                    <thead class="bg-slate-50">

                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Payment method
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Payment date
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-slate-500">
                                Amount
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($sale->payments as $payment)

                            <tr class="transition hover:bg-slate-50">

                                <td class="whitespace-nowrap px-5 py-4">

                                    <span class="inline-flex items-center gap-2
                                                 rounded-full bg-slate-100 px-3 py-1
                                                 text-xs font-medium text-slate-700">

                                        <i class="fa-solid fa-wallet text-slate-400"></i>

                                        {{ $payment->method }}

                                    </span>

                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">

                                    {{ $payment->payment_date }}

                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right
                                           font-semibold text-slate-800">

                                    {{ number_format((float) $payment->amount, 3) }}

                                    <span class="ml-1 text-xs font-medium text-slate-500">
                                        {{ $currency }}
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

</div>
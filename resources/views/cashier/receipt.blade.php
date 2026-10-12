{{-- Expects: $sale, $parametre --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Receipt #{{ $sale->id }}</title>

    <style>
        :root {
            --receipt-width: 72mm;
            --receipt-font-size: 12px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Courier New", monospace;
            width: var(--receipt-width);
            max-width: 100%;
            margin: 20px auto;
            padding: 0 2mm;
            color: #000;
            background: #fff;
            font-size: var(--receipt-font-size);
            line-height: 1.4;
        }

        .store-name {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            overflow-wrap: anywhere;
            margin-bottom: 4px;
        }

        .center {
            text-align: center;
        }

        .muted {
            font-size: 11px;
        }

        .receipt-info {
            margin-top: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td {
            padding: 3px 0;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .item-name {
            font-weight: bold;
            padding-top: 7px;
        }

        .right {
            text-align: right;
            white-space: nowrap;
        }

        .amount-label {
            width: 55%;
        }

        .amount-value {
            width: 45%;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 9px 0;
        }

        .total-row td {
            font-size: 15px;
            font-weight: bold;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .thank-you {
            margin-top: 14px;
            font-weight: bold;
        }

        .no-print {
            text-align: center;
            margin: 20px 0;
            font-family: Arial, sans-serif;
        }

        .no-print button {
            padding: 10px 16px;
            margin: 3px;
            cursor: pointer;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 3mm;
            }

            body {
                width: auto;
                max-width: none;
                margin: 0;
                padding: 0;
                font-size: 11px;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Uncomment this block when printing on a 58 mm printer.
        @media print {
            @page {
                size: 58mm auto;
                margin: 2mm;
            }

            body {
                font-size: 10px;
            }

            .store-name {
                font-size: 15px;
            }

            .total-row td {
                font-size: 13px;
            }
        }
        */
    </style>
</head>

<body>

    {{-- Store information --}}
    <header class="center">
        <div class="store-name">
            {{ $parametre?->store_name ?? 'POS' }}
        </div>

        @if ($parametre?->address)
            <div>{{ $parametre->address }}</div>
        @endif

        @if ($parametre?->phone)
            <div>Tel: {{ $parametre->phone }}</div>
        @endif

        @if ($parametre?->email)
            <div>{{ $parametre->email }}</div>
        @endif
    </header>

    <hr>

    {{-- Sale information --}}
    <section class="receipt-info center">
        <strong>SALES RECEIPT</strong>
        <br>
        Receipt #: {{ $sale->id }}
        <br>
        Date:
        {{ $sale->sale_date?->format('d/m/Y H:i') ?? $sale->sale_date }}
        <br>
        Cashier:
        {{ $sale->cashSession?->cashier?->name ?? 'N/A' }}
    </section>

    <hr>

    {{-- Purchased products --}}
    <table>
        <thead>
            <tr>
                <td><strong>Item</strong></td>
                <td class="right"><strong>Total</strong></td>
            </tr>
        </thead>

        <tbody>
            @forelse ($sale->items as $item)
                <tr>
                    <td colspan="2" class="item-name">
                        {{ $item->product?->name ?? 'Product unavailable' }}
                    </td>
                </tr>

                <tr>
                    <td>
                        {{ $item->quantity }}
                        x
                        {{ number_format((float) $item->unit_price, 3) }}
                    </td>

                    <td class="right">
                        {{ number_format((float) $item->quantity * (float) $item->unit_price, 3) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="center">
                        No products
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <hr>

    {{-- Payment summary --}}
    @php
        $currency = $parametre?->currency ?? 'TND';
        $totalPaid = (float) $sale->payments->sum('amount');
        $total = (float) $sale->total;
        $change = max(0, $totalPaid - $total);
    @endphp

    <table>
        <tbody>
            <tr class="total-row">
                <td class="amount-label">TOTAL</td>
                <td class="right amount-value">
                    {{ number_format($total, 3) }}
                </td>
            </tr>

            <tr>
                <td colspan="2" class="right muted">
                    Currency: {{ $currency }}
                </td>
            </tr>

            @foreach ($sale->payments as $payment)
                <tr>
                    <td>
                        {{ $payment->method }}
                    </td>

                    <td class="right">
                        {{ number_format((float) $payment->amount, 3) }}
                    </td>
                </tr>
            @endforeach

            <tr>
                <td>Amount paid</td>
                <td class="right">
                    {{ number_format($totalPaid, 3) }}
                </td>
            </tr>

            @if ($change > 0)
                <tr>
                    <td>Change</td>
                    <td class="right">
                        {{ number_format($change, 3) }}
                    </td>
                </tr>
            @elseif ($totalPaid < $total)
                <tr>
                    <td>Remaining</td>
                    <td class="right">
                        {{ number_format($total - $totalPaid, 3) }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <hr>

    <footer class="center thank-you">
        Thank you for your purchase!
        <br>
        <span class="muted">Please come again.</span>
    </footer>

    {{-- Browser controls: never printed --}}
    <div class="no-print">
        <button type="button" onclick="window.print()">
            Print Receipt
        </button>

        <button type="button" onclick="window.close()">
            Close
        </button>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>

</body>
</html>
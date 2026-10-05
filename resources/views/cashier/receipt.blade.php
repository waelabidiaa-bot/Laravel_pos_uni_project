{{-- Expects: $sale, $parametre --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>
        Receipt #{{ $sale->id }}
    </title>

    <style>
        body {
            font-family: monospace;
            width: 300px;
            margin: 20px auto;
            font-size: 13px;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            margin: 0 0 4px;
        }

        .center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    {{-- STORE INFORMATION --}}

    <h1>
        {{ $parametre?->store_name ?? 'POS' }}
    </h1>

    @if($parametre?->address)
        <div class="center">
            {{ $parametre->address }}
        </div>
    @endif

    @if($parametre?->phone)
        <div class="center">
            {{ $parametre->phone }}
        </div>
    @endif

    <div class="center">
        Receipt #{{ $sale->id }}
        <br>

        {{ $sale->sale_date }}
        <br>

        Cashier:
        {{ $sale->cashSession->cashier->name }}
    </div>

    <hr>

    {{-- PRODUCTS --}}

    <table>

        @foreach ($sale->items as $item)

            <tr>
                <td colspan="2">
                    {{ $item->product->name }}
                </td>
            </tr>

            <tr>
                <td>
                    {{ $item->quantity }}
                    ×
                    {{ number_format($item->unit_price, 3) }}
                </td>

                <td class="right">
                    {{ number_format($item->quantity * $item->unit_price, 3) }}
                </td>
            </tr>

        @endforeach

    </table>

    <hr>

    {{-- TOTAL --}}

    <table>

        <tr>
            <td>
                <strong>TOTAL</strong>
            </td>

            <td class="right">
                <strong>
                    {{ number_format($sale->total, 3) }}
                    {{ $parametre?->currency ?? 'TND' }}
                </strong>
            </td>
        </tr>

        {{-- PAYMENTS --}}

        @foreach ($sale->payments as $payment)

            <tr>
                <td>
                    {{ $payment->method }}
                </td>

                <td class="right">
                    {{ number_format($payment->amount, 3) }}
                    {{ $parametre?->currency ?? 'TND' }}
                </td>
            </tr>

        @endforeach

        {{-- CHANGE --}}

        @php
            $change = $sale->payments->sum('amount') - $sale->total;
        @endphp

        @if ($change > 0)

            <tr>
                <td>
                    Change
                </td>

                <td class="right">
                    {{ number_format($change, 3) }}
                    {{ $parametre?->currency ?? 'TND' }}
                </td>
            </tr>

        @endif

    </table>

    <hr>

    <div class="center">
        Thank you!
    </div>

    <p class="center no-print">
        <a href="#" onclick="window.close()">
            Close
        </a>
    </p>

</body>
</html>
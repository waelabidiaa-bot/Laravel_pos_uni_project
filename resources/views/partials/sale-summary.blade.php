{{-- Usage: @include('partials.sale-summary', ['sale' => $sale]) --}}
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="p-3">Product</th><th class="p-3 text-right">Qty</th><th class="p-3 text-right">Unit price</th><th class="p-3 text-right">Subtotal</th></tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr class="border-t">
                    <td class="p-3">{{ $item->product->name }}</td>
                    <td class="p-3 text-right">{{ $item->quantity }}</td>
                    <td class="p-3 text-right">{{ number_format($item->unit_price, 3) }}</td>
                    <td class="p-3 text-right">{{ number_format($item->quantity * $item->unit_price, 3) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t font-semibold"><td colspan="3" class="p-3 text-right">Total</td><td class="p-3 text-right">{{ number_format($sale->total, 3) }} TND</td></tr>
        </tfoot>
    </table>
</div>

<h3 class="font-semibold mb-2">Payments</h3>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <tbody>
            @forelse ($sale->payments as $payment)
                <tr class="border-t first:border-0">
                    <td class="p-3">{{ $payment->method }}</td>
                    <td class="p-3 text-gray-500">{{ $payment->payment_date }}</td>
                    <td class="p-3 text-right">{{ number_format($payment->amount, 3) }} TND</td>
                </tr>
            @empty
                <tr><td class="p-3 text-gray-500">No payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

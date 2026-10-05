<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, Sale $sale)
    {
        abort_unless($sale->isOwnedBy($request->user()), 403);

        if ($sale->status !== 'PENDING' || $sale->items()->doesntExist()) {
            return back()->with('error', 'Nothing to pay for this sale.');
        }

        $data = $request->validate([
            'method' => ['required', 'in:CASH,CARD'],
            'amount' => ['required', 'numeric', 'min:0.001'],
        ]);

        $remaining = max(0, $sale->total - $sale->payments()->sum('amount'));

        // Cash can exceed the remaining amount (change is given); card cannot
        if ($data['method'] === 'CARD' && $data['amount'] > $remaining + 0.0005) {
            return back()->with('error', 'A card payment cannot exceed the remaining amount.');
        }

        try {
            DB::transaction(function () use ($sale, $data) {
                $sale->payments()->create([
                    'amount'       => $data['amount'],
                    'method'       => $data['method'],
                    'payment_date' => now(),
                ]);

                // Fully paid: take the goods out of stock and close the sale
                if ($sale->payments()->sum('amount') + 0.0005 >= $sale->total) {
                    foreach ($sale->items()->with('product')->get() as $item) {
                        $stock = Stock::where('product_id', $item->product_id)->lockForUpdate()->first();

                        if (! $stock || $stock->quantity < $item->quantity) {
                            throw new \RuntimeException("Not enough stock for {$item->product->name}.");
                        }

                        $stock->decrement('quantity', $item->quantity);
                    }

                    $sale->update(['status' => 'PAID']);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cashier.sales.show', $sale);
    }
}

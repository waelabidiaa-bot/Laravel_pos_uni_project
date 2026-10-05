<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleItemController extends Controller
{
    public function store(Request $request, Sale $sale)
    {
        abort_unless($sale->isOwnedBy($request->user()), 403);

        if ($sale->status !== 'PENDING') {
            return back()->with('error', 'This sale is closed.');
        }

        $data = $request->validate([
            'barcode'  => ['required', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        // Exact barcode first, then fall back to a name search
        $product = Product::with('stock')->where('barcode', $data['barcode'])->first()
            ?? Product::with('stock')->where('name', 'like', '%' . $data['barcode'] . '%')->first();

        if (! $product) {
            return back()->with('error', 'Product not found.');
        }

        $existing = $sale->items()->where('product_id', $product->id)->first();
        $newQty   = ($existing->quantity ?? 0) + $data['quantity'];
        $inStock  = $product->stock->quantity ?? 0;

        if ($newQty > $inStock) {
            return back()->with('error', "Only {$inStock} unit(s) of {$product->name} in stock.");
        }

        if ($existing) {
            $sale->items()->where('product_id', $product->id)->update(['quantity' => $newQty]);
        } else {
            $sale->items()->create([
                'product_id' => $product->id,
                'quantity'   => $data['quantity'],
                'unit_price' => $product->price, // freeze the price at sale time
            ]);
        }

        $sale->recalculateTotal();

        return back();
    }

    public function destroy(Request $request, Sale $sale, Product $product)
    {
        abort_unless($sale->isOwnedBy($request->user()), 403);

        if ($sale->status !== 'PENDING') {
            return back()->with('error', 'This sale is closed.');
        }

        $sale->items()->where('product_id', $product->id)->delete();
        $sale->recalculateTotal();

        return back();
    }
}

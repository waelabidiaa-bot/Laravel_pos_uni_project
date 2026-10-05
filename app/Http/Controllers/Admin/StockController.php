<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::with('stock')->orderBy('name')->paginate(20);

        return view('admin.stock.index', compact('products'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity'      => ['required', 'integer', 'min:0'],
            'min_threshold' => ['required', 'integer', 'min:0'],
        ]);

        Stock::updateOrCreate(['product_id' => $product->id], $data);

        return back()->with('success', "Stock updated for {$product->name}.");
    }
}

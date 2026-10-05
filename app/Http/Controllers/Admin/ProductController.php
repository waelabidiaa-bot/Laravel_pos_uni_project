<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'stock'])
            ->when($request->q, function ($query, $term) {
                $query->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('barcode', 'like', "%{$term}%"));
            })
            ->orderBy('name')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($data) {
            $product = Product::create([
                'name'        => $data['name'],
                'barcode'     => $data['barcode'] ?? null,
                'price'       => $data['price'],
                'category_id' => $data['category_id'],
            ]);

            Stock::create([
                'product_id'    => $product->id,
                'quantity'      => $data['quantity'] ?? 0,
                'min_threshold' => $data['min_threshold'] ?? 0,
            ]);
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('stock');
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate($this->rules($product));

        DB::transaction(function () use ($data, $product) {
            $product->update([
                'name'        => $data['name'],
                'barcode'     => $data['barcode'] ?? null,
                'price'       => $data['price'],
                'category_id' => $data['category_id'],
            ]);

            Stock::updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => $data['quantity'] ?? 0, 'min_threshold' => $data['min_threshold'] ?? 0]
            );
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->exists()) {
            return back()->with('error', 'This product appears in past sales and cannot be deleted.');
        }

        $product->delete(); // stock row is removed by the foreign key cascade

        return back()->with('success', 'Product deleted.');
    }

    private function rules(?Product $product = null): array
    {
        return [
            'name'          => ['required', 'string', 'max:150'],
            'barcode'       => ['nullable', 'string', 'max:50', Rule::unique('products', 'barcode')->ignore($product?->id)],
            'price'         => ['required', 'numeric', 'min:0'],
            'category_id'   => ['required', 'exists:categories,id'],
            'quantity'      => ['nullable', 'integer', 'min:0'],
            'min_threshold' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

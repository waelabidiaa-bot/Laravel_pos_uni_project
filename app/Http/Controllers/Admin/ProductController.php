<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
{
    $request->validate([
        'q' => ['nullable', 'string', 'max:150'],
        'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        'stock_status' => ['nullable', 'in:all,available,low,out'],
    ]);

    $search = trim($request->input('q', ''));
    $categoryId = $request->input('category_id');
    $stockStatus = $request->input('stock_status', 'all');

    $products = Product::with(['category', 'stock'])
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        })
        ->when($categoryId, function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })
        ->when($stockStatus === 'available', function ($query) {
            $query->whereHas('stock', function ($stockQuery) {
                $stockQuery->where('quantity', '>', 0);
            });
        })
        ->when($stockStatus === 'out', function ($query) {
            $query->whereHas('stock', function ($stockQuery) {
                $stockQuery->where('quantity', '<=', 0);
            });
        })
        ->when($stockStatus === 'low', function ($query) {
            $query->whereHas('stock', function ($stockQuery) {
                $stockQuery->whereColumn(
                    'quantity',
                    '<=',
                    'min_threshold'
                )->where('quantity', '>', 0);
            });
        })
        ->orderBy('name')
        ->paginate(15)
        ->withQueryString();

    $categories = Category::orderBy('name')->get();

    return view('admin.products.index', compact(
        'products',
        'categories',
        'search',
        'categoryId',
        'stockStatus'
    ));
}
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
{
    $data = $request->validate($this->rules());

    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public');
    }

    try {
        DB::transaction(function () use ($data, $imagePath) {
            $product = Product::create([
                'name' => $data['name'],
                'barcode' => $data['barcode'] ?? null,
                'price' => $data['price'],
                'purchase_price' => $data['purchase_price'] ?? null,
                'category_id' => $data['category_id'],
                'image' => $imagePath,
            ]);

            Stock::create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'] ?? 0,
                'min_threshold' => $data['min_threshold'] ?? 0,
            ]);
        });
    } catch (\Throwable $exception) {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        throw $exception;
    }

    return redirect()
        ->route('admin.products.index')
        ->with('success', 'Product created successfully.');
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

    $oldImagePath = $product->image;
    $newImagePath = null;

    if ($request->hasFile('image')) {
        $newImagePath = $request->file('image')->store('products', 'public');
    }

    try {
        DB::transaction(function () use ($data, $product, $newImagePath) {
            $product->update([
                'name' => $data['name'],
                'barcode' => $data['barcode'] ?? null,
                'price' => $data['price'],
                'purchase_price' => $data['purchase_price'] ?? null,
                'category_id' => $data['category_id'],
                'image' => $newImagePath ?? $product->image,
            ]);

            Stock::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity' => $data['quantity'] ?? 0,
                    'min_threshold' => $data['min_threshold'] ?? 0,
                ]
            );
        });
    } catch (\Throwable $exception) {
        if ($newImagePath) {
            Storage::disk('public')->delete($newImagePath);
        }

        throw $exception;
    }

    if ($newImagePath && $oldImagePath) {
        Storage::disk('public')->delete($oldImagePath);
    }

    return redirect()
        ->route('admin.products.index')
        ->with('success', 'Product updated successfully.');
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
        'name' => ['required', 'string', 'max:150'],

        'barcode' => [
            'nullable',
            'string',
            'max:50',
            Rule::unique('products', 'barcode')->ignore($product?->id),
        ],

        'price' => ['required', 'numeric', 'min:0'],

        'purchase_price' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'category_id' => [
            'required',
            'exists:categories,id',
        ],

        'quantity' => [
            'nullable',
            'integer',
            'min:0',
        ],

        'min_threshold' => [
            'nullable',
            'integer',
            'min:0',
        ],

        'image' => [
            'nullable',
            'image',
            'mimes:jpeg,jpg,png,webp,gif',
            'max:2048',
        ],
    ];
}
}

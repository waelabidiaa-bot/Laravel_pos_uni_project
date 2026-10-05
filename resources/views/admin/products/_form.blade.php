{{-- Expects: $action, $method, $categories, optional $product --}}
<form method="POST" action="{{ $action }}" class="bg-white rounded-lg shadow p-6 max-w-lg space-y-4">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div>
        <label class="block text-sm mb-1">Name</label>
        <input name="name" value="{{ old('name', $product->name ?? '') }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm mb-1">Barcode</label>
        <input name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm mb-1">Price (TND)</label>
        <input name="price" type="number" step="0.001" min="0" value="{{ old('price', $product->price ?? '') }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm mb-1">Category</label>
        <select name="category_id" required class="w-full border rounded px-3 py-2">
            <option value="">Choose…</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? null) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm mb-1">Stock quantity</label>
            <input name="quantity" type="number" min="0" value="{{ old('quantity', $product->stock->quantity ?? 0) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Low-stock threshold</label>
            <input name="min_threshold" type="number" min="0" value="{{ old('min_threshold', $product->stock->min_threshold ?? 0) }}" class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <div class="flex gap-3">
        <button class="bg-sky-600 hover:bg-sky-700 text-white rounded px-4 py-2">Save</button>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-gray-600">Cancel</a>
    </div>
</form>

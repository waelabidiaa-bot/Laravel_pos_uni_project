{{-- Expects: $action, $method, $categories, optional $product --}}


<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    class="max-w-3xl space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
>
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-2 font-semibold">Please correct the following errors:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Product name --}}
    <div>
        <label for="name" class="mb-1 block text-sm font-semibold text-slate-700">
            Product name *
        </label>

        <input
            id="name"
            name="name"
            value="{{ old('name', $product->name ?? '') }}"
            required
            maxlength="150"
            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
            placeholder="e.g. Mineral Water 1.5L"
        >
    </div>

    {{-- Barcode --}}
    <div>
        <label for="barcode" class="mb-1 block text-sm font-semibold text-slate-700">
            Barcode
        </label>

        <input
            id="barcode"
            name="barcode"
            value="{{ old('barcode', $product->barcode ?? '') }}"
            maxlength="50"
            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
            placeholder="Scan or enter barcode"
        >
    </div>

    {{-- Category --}}
    <div>
        <label for="category_id" class="mb-1 block text-sm font-semibold text-slate-700">
            Category *
        </label>

        <select
            id="category_id"
            name="category_id"
            required
            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
        >
            <option value="">Choose a category</option>

            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected(old('category_id', $product->category_id ?? '') == $category->id)
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Prices --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="purchase_price" class="mb-1 block text-sm font-semibold text-slate-700">
                Purchase price (TND)
            </label>

            <input
                id="purchase_price"
                name="purchase_price"
                type="number"
                step="0.001"
                min="0"
                value="{{ old('purchase_price', $product->purchase_price ?? '') }}"
                class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                placeholder="0.000"
            >

            <p class="mt-1 text-xs text-slate-500">
                What your shop pays for one unit.
            </p>
        </div>

        <div>
            <label for="price" class="mb-1 block text-sm font-semibold text-slate-700">
                Selling price (TND) *
            </label>

            <input
                id="price"
                name="price"
                type="number"
                step="0.001"
                min="0"
                value="{{ old('price', $product->price ?? '') }}"
                required
                class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                placeholder="0.000"
            >

            <p class="mt-1 text-xs text-slate-500">
                The price charged to the customer.
            </p>
        </div>
    </div>

    {{-- Live profit preview --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-sm font-semibold text-slate-700">Profit estimate per unit</p>

        <div class="mt-3 grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-500">Profit</p>
                <p id="profit-preview" class="mt-1 text-xl font-bold text-slate-800">
                    —
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Profit margin</p>
                <p id="margin-preview" class="mt-1 text-xl font-bold text-slate-800">
                    —
                </p>
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-500">
            This estimate excludes other expenses such as rent, salaries, and taxes.
        </p>
    </div>

    {{-- Product image --}}
    <div>
        <label for="image" class="mb-2 block text-sm font-semibold text-slate-700">
            Product image
        </label>

        @if (!empty($product?->image))
            <div class="mb-3">
                <p class="mb-2 text-xs text-slate-500">Current image</p>

                <img
                    src="{{ asset('storage/' . $product->image) }}"
                    alt="{{ $product->name }}"
                    class="h-32 w-32 rounded-xl border border-slate-200 bg-white object-contain p-2"
                >
            </div>
        @endif

        <input
            id="image"
            name="image"
            type="file"
            accept="image/jpeg,image/png,image/webp,image/gif"
            class="block w-full rounded-xl border border-slate-300 p-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:font-semibold file:text-sky-700 hover:file:bg-sky-100"
        >

        <p class="mt-1 text-xs text-slate-500">
            JPEG, PNG, WebP, or GIF. Maximum size: 2 MB.
            Leave empty when editing to keep the current image.
        </p>

        <div id="image-preview-container" class="mt-3 hidden">
            <p class="mb-2 text-xs text-slate-500">New image preview</p>

            <img
                id="image-preview"
                alt="Selected product image preview"
                class="h-32 w-32 rounded-xl border border-slate-200 bg-white object-contain p-2"
            >
        </div>
    </div>

    {{-- Stock --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="quantity" class="mb-1 block text-sm font-semibold text-slate-700">
                Stock quantity
            </label>

            <input
                id="quantity"
                name="quantity"
                type="number"
                min="0"
                step="1"
                value="{{ old('quantity', $product->stock->quantity ?? 0) }}"
                class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
            >
        </div>

        <div>
            <label for="min_threshold" class="mb-1 block text-sm font-semibold text-slate-700">
                Low-stock threshold
            </label>

            <input
                id="min_threshold"
                name="min_threshold"
                type="number"
                min="0"
                step="1"
                value="{{ old('min_threshold', $product->stock->min_threshold ?? 0) }}"
                class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
            >
        </div>
    </div>

    {{-- Buttons --}}
    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-5">
        <button
            type="submit"
            class="rounded-xl bg-sky-600 px-5 py-3 font-semibold text-white transition hover:bg-sky-700"
        >
            Save product
        </button>

        <a
            href="{{ route('admin.products.index') }}"
            class="rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-700 transition hover:bg-slate-50"
        >
            Cancel
        </a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const purchaseInput = document.getElementById('purchase_price');
        const sellingInput = document.getElementById('price');
        const profitPreview = document.getElementById('profit-preview');
        const marginPreview = document.getElementById('margin-preview');

        function updateProfitPreview() {
            const purchaseText = purchaseInput.value;
            const sellingText = sellingInput.value;

            if (purchaseText === '' || sellingText === '') {
                profitPreview.textContent = '—';
                marginPreview.textContent = '—';
                return;
            }

            const purchase = Number(purchaseText);
            const selling = Number(sellingText);

            if (!Number.isFinite(purchase) || !Number.isFinite(selling)) {
                profitPreview.textContent = '—';
                marginPreview.textContent = '—';
                return;
            }

            const profit = selling - purchase;
            const margin = selling > 0 ? (profit / selling) * 100 : null;

            profitPreview.textContent =
                profit.toFixed(3) + ' TND';

            marginPreview.textContent =
                margin === null ? '—' : margin.toFixed(2) + '%';

            profitPreview.className =
                'mt-1 text-xl font-bold ' +
                (profit < 0 ? 'text-red-600' : 'text-emerald-600');

            marginPreview.className =
                'mt-1 text-xl font-bold ' +
                (margin !== null && margin < 0 ? 'text-red-600' : 'text-emerald-600');
        }

        purchaseInput.addEventListener('input', updateProfitPreview);
        sellingInput.addEventListener('input', updateProfitPreview);
        updateProfitPreview();

        const imageInput = document.getElementById('image');
        const previewContainer = document.getElementById('image-preview-container');
        const preview = document.getElementById('image-preview');
        let previewUrl = null;

        imageInput.addEventListener('change', function () {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }

            const file = imageInput.files[0];

            if (!file) {
                previewContainer.classList.add('hidden');
                return;
            }

            if (!file.type.startsWith('image/')) {
                imageInput.value = '';
                previewContainer.classList.add('hidden');
                alert('Please select an image file.');
                return;
            }

            previewUrl = URL.createObjectURL(file);
            preview.src = previewUrl;
            previewContainer.classList.remove('hidden');
        });
    });
</script>

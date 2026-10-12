<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

new class extends Component
{
    public Sale $sale;

    public string $search = '';

    public string $barcode = '';

    public int $quantity = 1;
    /**
     * Initialize the component and verify sale ownership.
     */
    public function mount(Sale $sale): void
    {
        abort_unless(
            auth()->check() && $sale->isOwnedBy(auth()->user()),
            403
        );

        $this->sale = $sale;
    }

    /**
     * Search products by name or barcode.
     */
    public function getProductsProperty()
    {
        $search = trim($this->search);

        if ($search === '') {
            return collect();
        }

        return Product::query()
            ->with('stock')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    /**
     * Reload the sale and its relationships.
     */
    private function refreshSale(): void
    {
        $sale = Sale::query()
            ->with(['items.product', 'payments'])
            ->findOrFail($this->sale->id);

        abort_unless(
            auth()->check() && $sale->isOwnedBy(auth()->user()),
            403
        );

        $this->sale = $sale;
    }

    /**
     * Notify the payment summary that the cart changed.
     */
    private function notifyCartUpdated(): void
    {
        $this->refreshSale();

        $this->dispatch(
            'cart-updated',
            saleId: $this->sale->id
        );
    }
    /**
 * Find a product by its exact barcode and add it to the cart.
 */
    public function scanBarcode(): void
    {
    $code = trim($this->barcode);

    if ($code === '') {
        $this->addError('barcode', 'Please scan a product barcode.');
        $this->dispatch('focus-barcode-input');
        return;
    }

    $product = Product::query()
        ->where('barcode', $code)
        ->first();

    if (! $product) {
        $this->addError(
            'barcode',
            "No product found with barcode: {$code}"
        );

        $this->barcode = '';

        $this->dispatch('focus-barcode-input');

        return;
    }

    // Each scan adds one unit.
    $this->quantity = 1;

    $this->resetValidation('barcode');

    // Reuse the existing method, including its stock checks.
    $this->addProduct((int) $product->id);

    $this->barcode = '';

    // Prepare for the next scan.
    $this->dispatch('focus-barcode-input');
}

    /**
     * Add a product to the current sale.
     */
    public function addProduct(int $productId): void
{
    // Clear previous validation errors.
    $this->resetValidation();

    // Validate the quantity.
    $this->validate([
        'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
    ]);

    // Add the product inside a database transaction.
    $added = DB::transaction(function () use ($productId): bool {

        // Lock the sale to prevent simultaneous modifications.
        $sale = Sale::query()
            ->whereKey($this->sale->id)
            ->lockForUpdate()
            ->firstOrFail();

        // Make sure the sale belongs to the connected cashier.
        abort_unless($sale->isOwnedBy(auth()->user()), 403);

        // Do not allow changes to a closed or paid sale.
        if ($sale->status !== 'PENDING') {
            $this->addError('cart', 'This sale is no longer open.');
            return false;
        }

        // Find the product.
        $product = Product::query()
            ->whereKey($productId)
            ->firstOrFail();

        // Lock the product's stock record.
        $stock = $product->stock()
            ->lockForUpdate()
            ->first();

        // Check that stock information exists.
        if (! $stock) {
            $this->addError(
                'cart',
                "Stock information is missing for {$product->name}."
            );

            return false;
        }

        // Find this product in the current sale, if already added.
        $item = $sale->items()
            ->where('product_id', $product->id)
            ->first();

        // Calculate the new quantity.
        $newQuantity = ($item?->quantity ?? 0) + $this->quantity;

        // Make sure sufficient stock is available.
        if ($newQuantity > $stock->quantity) {
            $this->addError(
                'cart',
                "Only {$stock->quantity} unit(s) of {$product->name} are available."
            );

            return false;
        }

        // Update the existing item or create a new one.
        if ($item) {
            $item->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            $sale->items()->create([
                'product_id' => $product->id,
                'quantity' => $this->quantity,
                'unit_price' => $product->price,
                'purchase_price_at_sale' => $product->purchase_price,
            ]);
        }

        // Recalculate the sale total.
        $sale->recalculateTotal();

        return true;
    });

    // Stop if the product could not be added.
    if (! $added) {
        return;
    }

    // Reset the inputs after a successful addition.
    $this->quantity = 1;
    $this->search = '';

    // Refresh the sale and its relationships.
    $this->sale->refresh()->load([
        'items.product',
        'payments',
    ]);

    // Notify the payment summary that the cart changed.
    $this->dispatch('cart-updated', saleId: $this->sale->id);
}
    /**
     * Update an item's quantity.
     */
    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity < 1 || $quantity > 1000) {
            $this->addError(
                'cart',
                'Quantity must be between 1 and 1000.'
            );

            return;
        }

        DB::transaction(function () use ($productId, $quantity) {
            $sale = Sale::query()
                ->whereKey($this->sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                auth()->check() && $sale->isOwnedBy(auth()->user()),
                403
            );

            if ($sale->status !== 'PENDING') {
                $this->addError(
                    'cart',
                    'This sale is no longer open.'
                );

                return;
            }

            $item = $sale->items()
                ->where('product_id', $productId)
                ->firstOrFail();

            $stock = Stock::query()
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                $this->addError(
                    'cart',
                    'Stock information is missing.'
                );

                return;
            }

            if ($quantity > $stock->quantity) {
                $this->addError(
                    'cart',
                    "Only {$stock->quantity} unit(s) are available."
                );

                return;
            }

            $item->update([
                'quantity' => $quantity,
            ]);

            $sale->recalculateTotal();
        });

        $this->resetValidation();

        $this->notifyCartUpdated();
    }

    /**
     * Remove a product from the cart.
     */
    public function removeProduct(int $productId): void
    {
        DB::transaction(function () use ($productId) {
            $sale = Sale::query()
                ->whereKey($this->sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                auth()->check() && $sale->isOwnedBy(auth()->user()),
                403
            );

            if ($sale->status !== 'PENDING') {
                $this->addError(
                    'cart',
                    'This sale is no longer open.'
                );

                return;
            }

            $sale->items()
                ->where('product_id', $productId)
                ->delete();

            $sale->recalculateTotal();
        });

        $this->resetValidation();

        $this->notifyCartUpdated();
    }

    /**
     * Refresh the cart when another component announces an update.
     */
    #[\Livewire\Attributes\On('cart-updated')]
    public function handleCartUpdated(int $saleId): void
    {
        if ($saleId !== $this->sale->id) {
            return;
        }

        $this->refreshSale();
    }
};
?>

<div class="space-y-5" wire:key="pos-cart-{{ $sale->id }}">

    @php
        $sale = \App\Models\Sale::with([
            'items.product',
            'payments',
        ])->findOrFail($sale->id);

        abort_unless(
            auth()->check() && $sale->isOwnedBy(auth()->user()),
            403
        );

        $isPending = $sale->status === 'PENDING';

        $currency = \App\Models\Parametre::first()?->currency ?? 'TND';
    @endphp

    {{-- Barcode scanner --}}
<div
    x-data
    x-on:focus-barcode-input.window="$nextTick(() => $refs.barcode.focus())"
    class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-4"
>
    <label
        for="pos-barcode"
        class="mb-2 block text-sm font-semibold text-gray-800"
    >
        <i class="fa-solid fa-barcode mr-2"></i>
        Scan product barcode
    </label>

    <div class="flex gap-2">
        <input
            id="pos-barcode"
            x-ref="barcode"
            type="text"
            wire:model="barcode"
            wire:keydown.enter.prevent="scanBarcode"
            placeholder="Scan or enter barcode..."
            autocomplete="off"
            autofocus
            class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
        >

        <button
            type="button"
            wire:click="scanBarcode"
            wire:loading.attr="disabled"
            wire:target="scanBarcode"
            class="rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
        >
            <i class="fa-solid fa-barcode mr-1"></i>
            Scan
        </button>
    </div>

    @error('barcode')
        <p class="mt-2 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror

    <p class="mt-2 text-xs text-gray-500">
        Scan a product barcode and press Enter. The product will be added to the cart automatically.
    </p>
</div>

    {{-- Product search --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <h2 class="mb-3 font-bold text-slate-900">
            Find a product
        </h2>

        @if ($isPending)

            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by product name or barcode..."
                autocomplete="off"
                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >

            @error('search')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

            {{-- Search results --}}
            @if (trim($search) !== '')

                <div class="mt-3 divide-y divide-slate-100 rounded-xl border border-slate-200">

                    @forelse ($this->products as $product)

                        <div
                            wire:key="product-{{ $product->id }}"
                            class="flex items-center justify-between gap-4 p-3"
                        >

                            <div class="min-w-0">

                                <p class="font-semibold text-slate-900">
                                    {{ $product->name }}
                                </p>

                                <p class="text-sm text-slate-500">
                                    {{ $product->barcode ?: 'No barcode' }}
                                    ·
                                    {{ number_format($product->price, 3) }}
                                    {{ $currency }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    Available:
                                    {{ $product->stock?->quantity ?? 0 }}
                                </p>

                            </div>

                            <button
                                type="button"
                                wire:click="addProduct({{ $product->id }})"
                                wire:loading.attr="disabled"
                                wire:target="addProduct"
                                class="shrink-0 rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 disabled:cursor-wait disabled:opacity-50"
                            >

                                <span
                                    wire:loading.remove
                                    wire:target="addProduct"
                                >
                                    Add
                                </span>

                                <span
                                    wire:loading
                                    wire:target="addProduct"
                                >
                                    Adding...
                                </span>

                            </button>

                        </div>

                    @empty

                        <p class="p-4 text-sm text-slate-500">
                            No matching products found.
                        </p>

                    @endforelse

                </div>

            @endif

            {{-- Quantity to add --}}
            <div class="mt-4 flex items-center gap-3">

                <label
                    for="pos-quantity"
                    class="text-sm font-semibold text-slate-700"
                >
                    Quantity to add
                </label>

                <input
                    id="pos-quantity"
                    type="number"
                    min="1"
                    max="1000"
                    wire:model="quantity"
                    class="w-24 rounded-lg border border-slate-200 px-3 py-2"
                >

            </div>

        @else

            <p class="text-sm text-slate-500">
                This sale is closed. Products can no longer be added.
            </p>

        @endif

        @error('cart')
            <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                {{ $message }}
            </div>
        @enderror

    </section>

    {{-- Shopping cart --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 p-5">

            <h2 class="font-bold text-slate-900">
                Shopping Cart
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                {{ $sale->items->count() }} product line(s)
            </p>

        </div>

        {{-- Loading indicator --}}
        <div
            wire:loading.flex
            wire:target="addProduct,updateQuantity,removeProduct"
            class="items-center gap-2 bg-blue-50 p-3 text-sm font-medium text-blue-700"
        >
            <i class="fa-solid fa-spinner fa-spin"></i>
            Updating cart...
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-left text-sm">

                <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                    <tr>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3 text-right">Quantity</th>
                        <th class="px-4 py-3 text-right">Price</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>

                        @if ($isPending)
                            <th class="px-4 py-3"></th>
                        @endif
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($sale->items as $item)

                        <tr wire:key="sale-item-{{ $item->product_id }}">

                            <td class="px-4 py-4 font-medium text-slate-900">
                                {{ $item->product->name ?? 'Product unavailable' }}
                            </td>

                            {{-- Quantity controls --}}
                            <td class="px-4 py-4 text-right">

                                @if ($isPending)

                                    <div class="flex items-center justify-end gap-2">

                                        <button
                                            type="button"
                                            aria-label="Decrease quantity"
                                            wire:click="updateQuantity({{ $item->product_id }}, {{ $item->quantity - 1 }})"
                                            wire:loading.attr="disabled"
                                            wire:target="updateQuantity"
                                            @disabled($item->quantity <= 1)
                                            class="h-8 w-8 rounded border border-slate-200 disabled:opacity-40"
                                        >
                                            −
                                        </button>

                                        <span class="min-w-6 text-center">
                                            {{ $item->quantity }}
                                        </span>

                                        <button
                                            type="button"
                                            aria-label="Increase quantity"
                                            wire:click="updateQuantity({{ $item->product_id }}, {{ $item->quantity + 1 }})"
                                            wire:loading.attr="disabled"
                                            wire:target="updateQuantity"
                                            class="h-8 w-8 rounded border border-slate-200 disabled:opacity-40"
                                        >
                                            +
                                        </button>

                                    </div>

                                @else

                                    {{ $item->quantity }}

                                @endif

                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                {{ number_format($item->unit_price, 3) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right font-semibold">
                                {{ number_format($item->quantity * $item->unit_price, 3) }}
                            </td>

                            @if ($isPending)

                                <td class="px-4 py-4 text-right">

                                    <button
                                        type="button"
                                        wire:click="removeProduct({{ $item->product_id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="removeProduct"
                                        wire:confirm="Remove this product from the sale?"
                                        class="text-red-600 hover:text-red-800 disabled:opacity-50"
                                        aria-label="Remove product"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>

                                </td>

                            @endif

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="{{ $isPending ? 5 : 4 }}"
                                class="px-4 py-10 text-center text-slate-500"
                            >
                                Your cart is empty. Search for a product above to begin.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Cart total --}}
        <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 p-5">

            <span class="font-semibold text-slate-700">
                Total
            </span>

            <span class="text-xl font-bold text-slate-900">
                {{ number_format($sale->total, 3) }}
                {{ $currency }}
            </span>

        </div>

    </section>

</div>
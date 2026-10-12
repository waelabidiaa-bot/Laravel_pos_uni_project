<?php

use App\Models\Sale;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public int $saleId;

    public Sale $sale;

    public function mount(Sale $sale): void
    {
        abort_unless($sale->isOwnedBy(auth()->user()), 403);

        $this->saleId = $sale->id;
        $this->refreshSale();
    }

    #[On('cart-updated')]
    public function handleCartUpdated(int $saleId): void
    {
        if ($saleId !== $this->saleId) {
            return;
        }

        $this->refreshSale();
    }

    private function refreshSale(): void
    {
        $this->sale = Sale::with(['items.product', 'payments'])
            ->findOrFail($this->saleId);

        abort_unless($this->sale->isOwnedBy(auth()->user()), 403);
    }

    public function render()
    {
        return view('components.cashier.⚡payment-summary', [
            'sale' => $this->sale,
        ]);
    }
};
?>

<div>
    @php
        $isPending = $sale->status === 'PENDING';
        $paid = $sale->payments->sum('amount');
        $remaining = max(0, $sale->total - $paid);
        $currency = \App\Models\Parametre::first()?->currency ?? 'TND';
    @endphp

    {{-- Payment summary --}}
    <div class="overflow-hidden rounded-2xl bg-slate-900 text-white shadow-lg">
        <div class="p-6">
            <p class="text-sm font-medium text-slate-400">Amount to pay</p>

            <p class="mt-3 break-words text-3xl font-bold tracking-tight sm:text-4xl">
                {{ number_format($sale->total, 3) }}
            </p>

            <p class="mt-1 text-sm text-slate-400">{{ $currency }}</p>

            <div class="my-6 border-t border-white/10"></div>

            <div class="flex items-center justify-between gap-3 text-sm">
                <span class="text-slate-400">Total paid</span>
                <span class="font-semibold">
                    {{ number_format($paid, 3) }} {{ $currency }}
                </span>
            </div>

            <div class="mt-3 flex items-center justify-between gap-3 text-sm">
                <span class="text-slate-400">Remaining</span>
                <span class="font-semibold {{ $remaining > 0 ? 'text-amber-300' : 'text-emerald-300' }}">
                    {{ number_format($remaining, 3) }} {{ $currency }}
                </span>
            </div>
        </div>
                {{-- Reactive payment form --}}
        @if ($isPending && $sale->items->isNotEmpty())
            <div
                wire:key="payment-form-{{ $sale->id }}"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
            >
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>

                    <div>
                        <h2 class="font-bold text-slate-900">Take Payment</h2>
                        <p class="mt-1 text-xs text-slate-500">
                            Complete the transaction.
                        </p>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('cashier.sales.payments.store', $sale) }}"
                    class="space-y-4"
                >
                    @csrf

                    <div>
                        <label
                            for="payment-method-{{ $sale->id }}"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Payment method
                        </label>

                        <select
                            id="payment-method-{{ $sale->id }}"
                            name="method"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                        >
                            <option value="CASH">Cash</option>
                            <option value="CARD">Card</option>
                        </select>
                    </div>

                    <div>
                        <label
                            for="payment-amount-{{ $sale->id }}"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Amount received ({{ $currency }})
                        </label>

                        <input
                            id="payment-amount-{{ $sale->id }}"
                            name="amount"
                            type="number"
                            step="0.001"
                            min="0.001"
                            value="{{ number_format($remaining, 3, '.', '') }}"
                            required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-lg font-semibold text-slate-900 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Remaining amount:
                            <strong>
                                {{ number_format($remaining, 3) }} {{ $currency }}
                            </strong>
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                    >
                        <i class="fa-solid fa-check"></i>
                        Submit Payment
                    </button>
                </form>
            </div>
        @endif

        @if ($sale->status === 'PAID')
            <div class="bg-emerald-500/10 px-6 py-4 text-sm font-semibold text-emerald-300">
                <i class="fa-solid fa-circle-check mr-2"></i>
                Payment completed
            </div>
        @elseif ($sale->status === 'CANCELLED')
            <div class="bg-red-500/10 px-6 py-4 text-sm font-semibold text-red-300">
                <i class="fa-solid fa-circle-xmark mr-2"></i>
                This sale was cancelled
            </div>
        @endif
    </div>
</div>
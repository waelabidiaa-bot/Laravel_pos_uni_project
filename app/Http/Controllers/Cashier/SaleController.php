<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use App\Models\Parametre;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $session = $request->user()->openSession();

        if (! $session) {
            return redirect()->route('cashier.dashboard')->with('error', 'Open a cash session before selling.');
        }

        $sale = Sale::create([
            'cash_session_id' => $session->id,
            'sale_date'       => now(),
            'total'           => 0,
            'status'          => 'PENDING',
        ]);

        return redirect()->route('cashier.sales.show', $sale);
    }

    public function show(Request $request, Sale $sale)
    {
        abort_unless($sale->isOwnedBy($request->user()), 403);

        $sale->load(['items.product', 'payments']);

        return view('cashier.pos', compact('sale'));
    }

    public function cancel(Request $request, Sale $sale)
    {
        abort_unless($sale->isOwnedBy($request->user()), 403);

        if ($sale->status !== 'PENDING') {
            return back()->with('error', 'Only pending sales can be cancelled.');
        }

        if ($sale->payments()->exists()) {
            return back()->with('error', 'This sale already has a payment and cannot be cancelled.');
        }

        $sale->update(['status' => 'CANCELLED']);

        return redirect()->route('cashier.dashboard')->with('success', "Sale #{$sale->id} cancelled.");
    }

     public function receipt($id)
{
    $sale = Sale::with([
        'items.product',
        'payments',
        'cashSession.cashier'
    ])->findOrFail($id);

    $parametre = Parametre::first();

    return view('cashier.receipt', compact('sale', 'parametre'));
}
}

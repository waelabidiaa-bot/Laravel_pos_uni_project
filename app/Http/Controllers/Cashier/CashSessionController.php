<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use Illuminate\Http\Request;

class CashSessionController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->openSession()) {
            return back()->with('error', 'You already have an open session.');
        }

        $data = $request->validate(['opening_amount' => ['required', 'numeric', 'min:0']]);

        CashSession::create([
            'cashier_id'     => $user->id,
            'opened_at'      => now(),
            'opening_amount' => $data['opening_amount'],
            'status'         => 'OPEN',
        ]);

        return redirect()->route('cashier.dashboard')->with('success', 'Session opened.');
    }

    public function close(Request $request, CashSession $cashSession)
    {
        abort_unless($cashSession->cashier_id === $request->user()->id, 403);

        if ($cashSession->status !== 'OPEN') {
            return back()->with('error', 'This session is already closed.');
        }

        if ($cashSession->sales()->where('status', 'PENDING')->exists()) {
            return back()->with('error', 'Finish or cancel the pending sales before closing the session.');
        }

        $data = $request->validate(['closing_amount' => ['required', 'numeric', 'min:0']]);

        $cashSession->update([
            'closed_at'      => now(),
            'closing_amount' => $data['closing_amount'],
            'status'         => 'CLOSED',
        ]);

        return redirect()->route('cashier.dashboard')->with('success', 'Session closed.');
    }
}

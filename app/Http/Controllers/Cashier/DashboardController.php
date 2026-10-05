<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $session = $request->user()->openSession();
        $sales   = $session ? $session->sales()->orderByDesc('id')->get() : collect();

        return view('cashier.dashboard', compact('session', 'sales'));
    }
}

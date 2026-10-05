<?php

namespace App\Http\Controllers\comm;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $parametre = Parametre::first();

        return view('parametre.index', compact('parametre'));
    }

    public function update(Request $request)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        $request->validate([
            'store_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'currency' => 'required|string|max:10',
        ]);

        $parametre = Parametre::first();

        $parametre->update([
            'store_name' => $request->store_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'currency' => $request->currency,
        ]);

        return redirect()
            ->route('parametre.index')
            ->with('success', 'Paramètres mis à jour avec succès.');
    }
    
    public function updateTheme(Request $request)
{
    $request->validate([
        'theme' => 'required|in:light,dark',
    ]);

    auth()->user()->update([
        'theme' => $request->theme,
    ]);

    return response()->json([
        'success' => true,
        'theme' => $request->theme,
    ]);
}
}
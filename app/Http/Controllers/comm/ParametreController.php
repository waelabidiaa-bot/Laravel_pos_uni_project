<?php

namespace App\Http\Controllers\comm;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    /**
     * Display the store settings.
     */
    public function index()
    {
        $parametre = Parametre::firstOrCreate(
            [],
            [
                'store_name' => 'My Store',
                'address' => '',
                'phone' => '',
                'email' => '',
                'currency' => 'TND',
            ]
        );

        return view('parametre.index', compact('parametre'));
    }

    /**
     * Update the store settings.
     */
    public function update(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'currency' => 'required|string|max:10',
        ]);

        $parametre = Parametre::firstOrCreate(
            [],
            [
                'store_name' => 'My Store',
                'address' => '',
                'phone' => '',
                'email' => '',
                'currency' => 'TND',
            ]
        );

        $parametre->update($validated);

        return redirect()
            ->route('parametre.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Update the current user's theme.
     */
    public function updateTheme(Request $request)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $validated = $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        auth()->user()->update([
            'theme' => $validated['theme'],
        ]);

        return response()->json([
            'success' => true,
            'theme' => $validated['theme'],
        ]);
    }
}
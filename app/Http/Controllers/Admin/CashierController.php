<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CashierController extends Controller
{
    public function index()
    {
        $cashiers = User::where('role', 'CASHIER')->orderBy('name')->paginate(15);

        return view('admin.cashiers.index', compact('cashiers'));
    }

    public function create()
    {
        return view('admin.cashiers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'CASHIER',
        ]);

        return redirect()->route('admin.cashiers.index')->with('success', 'Cashier created.');
    }

    public function edit(User $cashier)
    {
        abort_unless($cashier->role === 'CASHIER', 404);

        return view('admin.cashiers.edit', compact('cashier'));
    }

    public function update(Request $request, User $cashier)
    {
        abort_unless($cashier->role === 'CASHIER', 404);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($cashier->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $cashier->name  = $data['name'];
        $cashier->email = $data['email'];
        if (! empty($data['password'])) {
            $cashier->password = Hash::make($data['password']);
        }
        $cashier->save();

        return redirect()->route('admin.cashiers.index')->with('success', 'Cashier updated.');
    }

    public function destroy(User $cashier)
    {
        abort_unless($cashier->role === 'CASHIER', 404);

        if ($cashier->cashSessions()->exists()) {
            return back()->with('error', 'This cashier has session history and cannot be deleted.');
        }

        $cashier->delete();

        return back()->with('success', 'Cashier deleted.');
    }
}

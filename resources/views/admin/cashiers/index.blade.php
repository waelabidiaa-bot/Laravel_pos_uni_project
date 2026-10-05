{{-- Expects: $cashiers (paginator of User) --}}
@extends('layouts.app')
@section('title', 'Cashiers')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Cashiers</h1>
    <a href="{{ route('admin.cashiers.create') }}" class="bg-sky-600 hover:bg-sky-700 text-white rounded px-4 py-2">New cashier</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Created</th><th class="p-3"></th></tr>
        </thead>
        <tbody>
            @foreach ($cashiers as $cashier)
                <tr class="border-t">
                    <td class="p-3">{{ $cashier->name }}</td>
                    <td class="p-3">{{ $cashier->email }}</td>
                    <td class="p-3">{{ $cashier->created_at->format('Y-m-d') }}</td>
                    <td class="p-3 text-right flex gap-3 justify-end">
                        <a href="{{ route('admin.cashiers.edit', $cashier) }}" class="text-sky-600">Edit</a>
                        <form method="POST" action="{{ route('admin.cashiers.destroy', $cashier) }}"
                              onsubmit="return confirm('Delete this cashier?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $cashiers->links() }}</div>
@endsection

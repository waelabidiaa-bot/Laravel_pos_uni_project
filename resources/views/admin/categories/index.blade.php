{{-- Expects: $categories (collection of Category with products_count) --}}
@extends('layouts.app')
@section('title', 'Categories')

@section('content')
<h1 class="text-2xl font-bold mb-6">Categories</h1>

<form method="POST" action="{{ route('admin.categories.store') }}" class="flex gap-3 mb-6">
    @csrf
    <input name="name" placeholder="New category name" required class="border rounded px-3 py-2 w-64">
    <button class="bg-sky-600 hover:bg-sky-700 text-white rounded px-4 py-2">Add</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="p-3">Name</th><th class="p-3">Products</th><th class="p-3"></th></tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr class="border-t">
                    <td class="p-3">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex gap-2">
                            @csrf @method('PUT')
                            <input name="name" value="{{ $category->name }}" class="border rounded px-2 py-1">
                            <button class="text-sky-600">Rename</button>
                        </form>
                    </td>
                    <td class="p-3">{{ $category->products_count }}</td>
                    <td class="p-3 text-right">
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                              onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

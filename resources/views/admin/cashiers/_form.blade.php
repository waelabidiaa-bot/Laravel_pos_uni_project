{{-- Expects: $action, $method ('POST'|'PUT'), optional $cashier --}}
<form method="POST" action="{{ $action }}" class="bg-white rounded-lg shadow p-6 max-w-lg space-y-4">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div>
        <label class="block text-sm mb-1">Name</label>
        <input name="name" value="{{ old('name', $cashier->name ?? '') }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm mb-1">Email</label>
        <input name="email" type="email" value="{{ old('email', $cashier->email ?? '') }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm mb-1">Password @isset($cashier)<span class="text-gray-400">(leave blank to keep)</span>@endisset</label>
        <input name="password" type="password" @empty($cashier) required @endempty class="w-full border rounded px-3 py-2">
    </div>

    <div class="flex gap-3">
        <button class="bg-sky-600 hover:bg-sky-700 text-white rounded px-4 py-2">Save</button>
        <a href="{{ route('admin.cashiers.index') }}" class="px-4 py-2 text-gray-600">Cancel</a>
    </div>
</form>

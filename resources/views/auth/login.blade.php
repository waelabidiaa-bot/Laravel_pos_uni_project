@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="max-w-sm mx-auto mt-16 bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold mb-4">Sign in</h1>
    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1" for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1" for="password">Password</label>
            <input id="password" name="password" type="password" required class="w-full border rounded px-3 py-2">
        </div>
        <button class="w-full bg-sky-600 hover:bg-sky-700 text-white rounded py-2">Login</button>
    </form>
</div>
@endsection

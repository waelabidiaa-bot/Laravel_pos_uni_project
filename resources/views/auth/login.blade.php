@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')

@php
    $storeName = \App\Models\Parametre::first()?->store_name ?? 'POS System';
@endphp

<div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 px-4 py-10 sm:px-6">

    {{-- Background decoration --}}
    <div class="pointer-events-none absolute -left-40 -top-40 h-96 w-96 rounded-full bg-blue-600/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-indigo-600/10 blur-3xl"></div>


    <div class="relative z-10 grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-white shadow-2xl lg:grid-cols-2">


        {{-- Left branding panel --}}
        <div class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-blue-700 via-blue-800 to-slate-950 p-10 text-white lg:flex">

            {{-- Decorative circles --}}
            <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full border border-white/10"></div>
            <div class="absolute -right-10 -top-10 h-52 w-52 rounded-full border border-white/10"></div>
            <div class="absolute -bottom-24 -left-20 h-80 w-80 rounded-full bg-blue-500/10"></div>


            {{-- Brand --}}
            <div class="relative z-10 flex items-center gap-3">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/20 bg-white/10 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-cash-register text-xl"></i>
                </div>

                <div>
                    <p class="text-lg font-bold tracking-wide">
                        {{ $storeName }}
                    </p>

                    <p class="text-xs text-blue-200">
                        Point of Sale Management
                    </p>
                </div>

            </div>


            {{-- Main branding content --}}
            <div class="relative z-10 my-12">

                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-medium text-blue-100">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Your business, simplified
                </span>

                <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight">
                    Manage your store.
                    <br>

                    <span class="text-blue-300">
                        Make every sale count.
                    </span>
                </h1>

                <p class="mt-5 max-w-sm text-sm leading-7 text-blue-100/80">
                    Access your point of sale dashboard to manage products,
                    monitor inventory, process transactions, and understand
                    your business performance.
                </p>


                {{-- Feature list --}}
                <div class="mt-8 space-y-4">

                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10">
                            <i class="fa-solid fa-chart-line text-sm text-blue-200"></i>
                        </div>

                        <span class="text-sm text-blue-50">
                            Sales and financial insights
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10">
                            <i class="fa-solid fa-boxes-stacked text-sm text-blue-200"></i>
                        </div>

                        <span class="text-sm text-blue-50">
                            Inventory and product management
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10">
                            <i class="fa-solid fa-shield-halved text-sm text-blue-200"></i>
                        </div>

                        <span class="text-sm text-blue-50">
                            Role-based access for your team
                        </span>
                    </div>

                </div>

            </div>


            {{-- Footer --}}
            <div class="relative z-10 border-t border-white/10 pt-5">
                <p class="text-xs text-blue-200/70">
                    &copy; {{ date('Y') }} {{ $storeName }}.
                    All rights reserved.
                </p>
            </div>

        </div>


        {{-- Right login panel --}}
        <div class="flex flex-col justify-center bg-white px-6 py-10 sm:px-10 lg:px-12">

            {{-- Mobile brand --}}
            <div class="mb-10 flex items-center gap-3 lg:hidden">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                    <i class="fa-solid fa-cash-register text-xl"></i>
                </div>

                <div>
                    <p class="text-lg font-bold text-slate-900">
                        {{ $storeName }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Point of Sale Management
                    </p>
                </div>

            </div>


            {{-- Heading --}}
            <div>

                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <i class="fa-solid fa-right-to-bracket text-2xl"></i>
                </div>

                <h2 class="text-3xl font-bold tracking-tight text-slate-900">
                    Welcome back
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Sign in to access your POS workspace.
                </p>

            </div>


            {{-- General error message --}}
            @if ($errors->any())
                <div class="mt-6 flex gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">

                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                    <div>
                        <p class="font-semibold">
                            Unable to sign in
                        </p>

                        <p class="mt-1">
                            {{ $errors->first() }}
                        </p>
                    </div>

                </div>
            @endif


            {{-- Session status --}}
            @if (session('status'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif


            {{-- Login form --}}
            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">

                @csrf


                {{-- Email --}}
                <div>

                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                        Email address
                    </label>

                    <div class="relative">

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fa-regular fa-envelope"></i>
                        </div>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            autocomplete="username"
                            required
                            autofocus
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            class="w-full rounded-xl border bg-white py-3.5 pl-11 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 {{ $errors->has('email') ? 'border-red-400' : 'border-slate-200' }}"
                        >

                    </div>

                    @error('email')
                        <p class="mt-2 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Password --}}
                <div>

                    <div class="mb-2 flex items-center justify-between">

                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Password
                        </label>

                    </div>

                    <div class="relative">

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            class="w-full rounded-xl border bg-white py-3.5 pl-11 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 {{ $errors->has('password') ? 'border-red-400' : 'border-slate-200' }}"
                        >

                        <button
                            type="button"
                            onclick="togglePassword()"
                            aria-label="Show password"
                            aria-controls="password"
                            id="password-toggle"
                            class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600"
                        >
                            <i id="password-icon" class="fa-regular fa-eye"></i>
                        </button>

                    </div>

                    @error('password')
                        <p class="mt-2 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Submit --}}
                <button
                    type="submit"
                    class="group flex w-full items-center justify-center gap-3 rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/20 focus:outline-none focus:ring-4 focus:ring-blue-500/30"
                >
                    <span>Sign in to your account</span>

                    <i class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                </button>

            </form>


            {{-- Security note --}}
            <div class="mt-8 flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-4">

                <i class="fa-solid fa-shield-halved mt-0.5 text-blue-600"></i>

                <div>
                    <p class="text-xs font-semibold text-slate-700">
                        Secure access
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Sign in using the credentials assigned to your account.
                        Your access depends on your assigned role.
                    </p>
                </div>

            </div>


            {{-- Footer --}}
            <p class="mt-8 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} {{ $storeName }}
            </p>

        </div>

    </div>

</div>


{{-- Password visibility toggle --}}
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('password-icon');
        const button = document.getElementById('password-toggle');

        const isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';

        icon.classList.toggle('fa-eye', !isHidden);
        icon.classList.toggle('fa-eye-slash', isHidden);

        button.setAttribute(
            'aria-label',
            isHidden ? 'Hide password' : 'Show password'
        );
    }
</script>

@endsection
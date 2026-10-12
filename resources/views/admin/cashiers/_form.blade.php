{{-- Expects: $action, $method ('POST'|'PUT'), optional $cashier --}}

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    {{-- Name --}}
    <div>
        <label for="name"
               class="mb-2 block text-sm font-semibold text-slate-700">
            Full name
            <span class="text-rose-500">*</span>
        </label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $cashier->name ?? '') }}"
            placeholder="Enter the cashier's full name"
            autocomplete="name"
            required
            autofocus
            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200 @error('name') border-rose-400 focus:border-rose-400 focus:ring-rose-100 @enderror"
        >

        @error('name')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div>
        <label for="email"
               class="mb-2 block text-sm font-semibold text-slate-700">
            Email address
            <span class="text-rose-500">*</span>
        </label>

        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $cashier->email ?? '') }}"
            placeholder="cashier@example.com"
            autocomplete="email"
            required
            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200 @error('email') border-rose-400 focus:border-rose-400 focus:ring-rose-100 @enderror"
        >

        @error('email')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror

        <p class="mt-2 text-xs text-slate-500">
            This email address will be used to sign in.
        </p>
    </div>

    {{-- Password --}}
    <div>
        <label for="password"
               class="mb-2 block text-sm font-semibold text-slate-700">
            Password

            @if (isset($cashier))
                <span class="font-normal text-slate-400">
                    (optional)
                </span>
            @else
                <span class="text-rose-500">*</span>
            @endif
        </label>

        <input
            id="password"
            name="password"
            type="password"
            autocomplete="{{ isset($cashier) ? 'new-password' : 'new-password' }}"
            placeholder="{{ isset($cashier) ? 'Leave empty to keep the current password' : 'Create a password' }}"
            @empty($cashier) required @endempty
            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200 @error('password') border-rose-400 focus:border-rose-400 focus:ring-rose-100 @enderror"
        >

        @error('password')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror

        @if (isset($cashier))
            <p class="mt-2 text-xs text-slate-500">
                Leave this field empty to keep the existing password.
            </p>
        @else
            <p class="mt-2 text-xs text-slate-500">
                Choose a strong password for the new account.
            </p>
        @endif
    </div>

    {{-- Form actions --}}
    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-end">

        <a href="{{ route('admin.cashiers.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            Cancel
        </a>

        <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m5 12 4 4L19 6" />
            </svg>

            {{ isset($cashier) ? 'Save changes' : 'Create cashier' }}
        </button>

    </div>

</form>

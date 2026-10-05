

@extends('layouts.app')

@section('title', 'Settings')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Settings
        </h1>

        <p class="text-slate-500">
            Customize your POS settings and appearance.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-100 border border-green-200 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- ADMIN ONLY --}}
    @if(auth()->user()->role === 'ADMIN')

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">

            <h2 class="text-lg font-semibold text-slate-800 mb-1">
                Store Settings
            </h2>

            <p class="text-sm text-slate-500 mb-6">
                Manage the information used throughout your POS.
            </p>

            <form action="{{ route('parametre.update') }}" method="POST">

                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">
                            Store Name
                        </label>

                        <input
                            type="text"
                            name="store_name"
                            value="{{ old('store_name', $parametre->store_name) }}"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Address
                        </label>

                        <input
                            type="text"
                            name="address"
                            value="{{ old('address', $parametre->address) }}"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $parametre->phone) }}"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $parametre->email) }}"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Currency
                        </label>

                        <select
                            name="currency"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5"
                        >
                            <option value="TND" {{ old('currency', $parametre->currency) === 'TND' ? 'selected' : '' }}>
                                TND
                            </option>

                            <option value="EUR" {{ old('currency', $parametre->currency) === 'EUR' ? 'selected' : '' }}>
                                EUR
                            </option>

                            <option value="USD" {{ old('currency', $parametre->currency) === 'USD' ? 'selected' : '' }}>
                                USD
                            </option>
                        </select>
                    </div>

                </div>

                <div class="mt-6 flex justify-end">

                    <button
                        type="submit"
                        class="bg-sky-500 hover:bg-sky-600 text-white px-6 py-2.5 rounded-lg"
                    >
                        <i class="fa-solid fa-save mr-2"></i>
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    @endif

    {{-- ADMIN + CASHIER --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">

        <h2 class="text-lg font-semibold text-slate-800 mb-1">
            Appearance
        </h2>

        <p class="text-sm text-slate-500 mb-6">
            Customize the appearance of your POS.
        </p>

        <div class="flex items-center justify-between">

            <div>
                <p class="font-medium text-slate-700">
                    Dark Mode
                </p>

                <p class="text-sm text-slate-500">
                    Enable dark mode for the application.
                </p>
            </div>

            <button
                id="theme-toggle"
                type="button"
                class="relative w-14 h-7 bg-slate-300 rounded-full transition"
            >
                <span
                    id="theme-toggle-circle"
                    class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full transition"
                ></span>
            </button>

        </div>

    </div>

</div>
<script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeCircle = document.getElementById('theme-toggle-circle');

    let currentTheme = "{{ auth()->user()->theme ?? 'light' }}";

    function applyTheme(theme) {

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');

            themeToggle.classList.remove('bg-slate-300');
            themeToggle.classList.add('bg-sky-500');

            themeCircle.classList.remove('left-1');
            themeCircle.classList.add('left-8');

        } else {
            document.documentElement.classList.remove('dark');

            themeToggle.classList.remove('bg-sky-500');
            themeToggle.classList.add('bg-slate-300');

            themeCircle.classList.remove('left-8');
            themeCircle.classList.add('left-1');
        }
    }

    applyTheme(currentTheme);

    themeToggle.addEventListener('click', async () => {

        const newTheme = currentTheme === 'light'
            ? 'dark'
            : 'light';

        const response = await fetch("{{ route('theme.update') }}", {
            method: 'PUT',

            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },

            body: JSON.stringify({
                theme: newTheme
            })
        });

        if (response.ok) {
            currentTheme = newTheme;
            applyTheme(currentTheme);
        }
    });
</script>


@endsection
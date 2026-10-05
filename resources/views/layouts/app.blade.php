
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

   <title>@yield('title', 'POS')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen">

@auth

<!-- NAVBAR -->
<nav class="bg-slate-900 text-white shadow-lg">

    <div class="max-w-7xl mx-auto px-6">

        <div class="h-16 flex items-center justify-between">

            <!-- LEFT -->
            <div class="flex items-center gap-8">

                <!-- LOGO -->
                <a href="{{ route('home') }}"
                   class="flex items-center gap-3 text-xl font-bold tracking-wide">

                    <div class="w-9 h-9 rounded-lg bg-sky-500 flex items-center justify-center">
                        <i class="fa-solid fa-store"></i>
                    </div>

                    <span>
                        {{ \App\Models\Parametre::first()?->store_name ?? 'POS' }}
                    </span>

                </a>


                <!-- NAVIGATION -->
                <div class="hidden md:flex items-center gap-1">

                    @if (auth()->user()->role === 'ADMIN')

                        <a href="{{ route('admin.dashboard') }}"
                           class="nav-link">
                            <i class="fa-solid fa-chart-line"></i>
                            Dashboard
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                           class="nav-link">
                            <i class="fa-solid fa-box"></i>
                            Products
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                           class="nav-link">
                            <i class="fa-solid fa-layer-group"></i>
                            Categories
                        </a>

                        <a href="{{ route('admin.stock.index') }}"
                           class="nav-link">
                            <i class="fa-solid fa-warehouse"></i>
                            Stock
                        </a>

                        <a href="{{ route('admin.cashiers.index') }}"
                           class="nav-link">
                            <i class="fa-solid fa-users"></i>
                            Cashiers
                        </a>

                        <a href="{{ route('admin.sales.index') }}"
                           class="nav-link">
                            <i class="fa-solid fa-receipt"></i>
                            Sales
                        </a>

                        <a href="{{ route('admin.statistics.index') }}"
                           class="nav-link">
                            <i class="fa-solid fa-chart-pie"></i>
                            Statistics
                        </a>

                    @else

                        <a href="{{ route('cashier.dashboard') }}"
                           class="nav-link">

                            <i class="fa-solid fa-cash-register"></i>
                            My Session

                        </a>

                    @endif

                </div>

            </div>


            <!-- RIGHT -->
            <div class="flex items-center gap-4">

                <!-- SETTINGS -->
                <a href="{{ route('parametre.index') }}"
                   title="Settings"
                   class="w-9 h-9 flex items-center justify-center rounded-lg
                          text-slate-300 hover:text-white
                          hover:bg-slate-800 transition">

                    <i class="fa-solid fa-gear"></i>

                </a>


                <!-- USER -->
                <div class="hidden sm:flex items-center gap-3">

                    <div class="w-9 h-9 rounded-full bg-sky-500
                                flex items-center justify-center
                                font-semibold">

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                    </div>

                    <div class="leading-tight">

                        <p class="text-sm font-medium">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-slate-400">
                            {{ strtolower(auth()->user()->role) }}
                        </p>

                    </div>

                </div>


                <!-- LOGOUT -->
                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button
                        class="w-9 h-9 flex items-center justify-center
                               rounded-lg text-slate-300
                               hover:text-white hover:bg-red-500/20
                               transition"
                        title="Logout">

                        <i class="fa-solid fa-right-from-bracket"></i>

                    </button>

                </form>

            </div>

        </div>

    </div>

</nav>


<!-- MAIN CONTENT -->

<main class="max-w-7xl mx-auto px-6 py-8">

    <!-- SUCCESS -->
    @if (session('success'))

        <div class="mb-6 flex items-center gap-3
                    rounded-xl border border-green-200
                    bg-green-50 px-5 py-4
                    text-green-800 shadow-sm">

            <i class="fa-solid fa-circle-check text-green-500"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    <!-- ERROR -->
    @if (session('error'))

        <div class="mb-6 flex items-center gap-3
                    rounded-xl border border-red-200
                    bg-red-50 px-5 py-4
                    text-red-800 shadow-sm">

            <i class="fa-solid fa-circle-exclamation text-red-500"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    <!-- VALIDATION ERRORS -->
    @if ($errors->any())

        <div class="mb-6 rounded-xl border border-red-200
                    bg-red-50 px-5 py-4
                    text-red-800 shadow-sm">

            <div class="flex items-center gap-3 mb-2">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <span class="font-semibold">
                    Please fix the following errors:
                </span>

            </div>

            <ul class="list-disc list-inside text-sm">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <!-- PAGE CONTENT -->

    @yield('content')

</main>

@endauth


<style>

    .nav-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        color: rgb(203 213 225);
        transition: all 0.2s;
    }

    .nav-link:hover {
        background-color: rgb(30 41 59);
        color: white;
    }

</style>
<script>
    tailwind.config = {
        darkMode: 'class'
    }
</script>

<script src="https://cdn.tailwindcss.com"></script>

</body>
</html>
```

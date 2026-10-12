<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dashboard') | {{ \App\Models\Parametre::first()?->store_name ?? 'POS' }}</title>

    {{-- Vite + Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Icons --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @livewireStyles
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

@auth

<div class="min-h-screen">

    {{-- MOBILE OVERLAY --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-40 hidden bg-slate-950/50 lg:hidden"
         onclick="toggleSidebar()">
    </div>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col
                  border-r border-slate-800 bg-slate-950 text-slate-300
                  transition-transform duration-300 lg:translate-x-0">

        {{-- BRAND --}}
        <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">

            <div class="flex h-10 w-10 items-center justify-center rounded-xl
                        bg-blue-600 text-lg text-white shadow-lg shadow-blue-600/20">
                <i class="fa-solid fa-store"></i>
            </div>

            <div class="min-w-0">
                <p class="truncate text-base font-bold tracking-wide text-white">
                    {{ \App\Models\Parametre::first()?->store_name ?? 'POS System' }}
                </p>

                <p class="mt-0.5 text-xs text-slate-500">
                    Management platform
                </p>
            </div>

            <button type="button"
                    onclick="toggleSidebar()"
                    class="ml-auto text-slate-400 hover:text-white lg:hidden"
                    aria-label="Close navigation">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

        </div>

        {{-- NAVIGATION --}}
        <nav class="flex-1 space-y-7 overflow-y-auto px-4 py-6">

            <div>
                <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">
                    Workspace
                </p>

                <div class="space-y-1">

                    @if (auth()->user()->role === 'ADMIN')

                        <a href="{{ route('admin.dashboard') }}"
                           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : '' }}">
                            <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                           class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'sidebar-link-active' : '' }}">
                            <i class="fa-solid fa-box w-5 text-center"></i>
                            <span>Products</span>
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                           class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'sidebar-link-active' : '' }}">
                            <i class="fa-solid fa-layer-group w-5 text-center"></i>
                            <span>Categories</span>
                        </a>

                        <a href="{{ route('admin.stock.index') }}"
                           class="sidebar-link {{ request()->routeIs('admin.stock.*') ? 'sidebar-link-active' : '' }}">
                            <i class="fa-solid fa-warehouse w-5 text-center"></i>
                            <span>Inventory</span>
                        </a>
                        
                        <a href="{{ route('admin.ai-insights.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.ai-insights.*') ? 'side-link-active' : '' }}">
                            <i class="fa-solid fa-robot"></i>
                            <span>AI Insights</span>
                        </a>


                        <a href="{{ route('admin.cashiers.index') }}"
                           class="sidebar-link {{ request()->routeIs('admin.cashiers.*') ? 'sidebar-link-active' : '' }}">
                            <i class="fa-solid fa-users w-5 text-center"></i>
                            <span>Cashiers</span>
                        </a>

                        <a href="{{ route('admin.sales.index') }}"
                           class="sidebar-link {{ request()->routeIs('admin.sales.*') ? 'sidebar-link-active' : '' }}">
                            <i class="fa-solid fa-receipt w-5 text-center"></i>
                            <span>Sales</span>
                        </a>

                        <a href="{{ route('admin.statistics.index') }}"
                           class="sidebar-link {{ request()->routeIs('admin.statistics.*') ? 'sidebar-link-active' : '' }}">
                            <i class="fa-solid fa-chart-line w-5 text-center"></i>
                            <span>Statistics</span>
                        </a>
                        

                    @else

                        <a href="{{ route('cashier.dashboard') }}"
                           class="sidebar-link {{ request()->routeIs('cashier.dashboard') ? 'sidebar-link-active' : '' }}">
                            <i class="fa-solid fa-cash-register w-5 text-center"></i>
                            <span>My Session</span>
                        </a>

                    @endif

                </div>
            </div>

            {{-- GENERAL --}}
            <div>
                <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">
                    General
                </p>

                <div class="space-y-1">

                    <a href="{{ route('parametre.index') }}"
                       class="sidebar-link {{ request()->routeIs('parametre.*') ? 'sidebar-link-active' : '' }}">
                        <i class="fa-solid fa-gear w-5 text-center"></i>
                        <span>Settings</span>
                    </a>

                </div>
            </div>

        </nav>

        {{-- SIDEBAR FOOTER --}}
        <div class="border-t border-white/10 p-4">

            <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center
                            rounded-xl bg-blue-500/15 font-semibold text-blue-400">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-white">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="mt-0.5 text-xs capitalize text-slate-500">
                        {{ strtolower(auth()->user()->role) }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                            title="Logout"
                            aria-label="Logout"
                            class="flex h-9 w-9 items-center justify-center rounded-lg
                                   text-slate-400 transition hover:bg-red-500/10
                                   hover:text-red-400">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>

            </div>

        </div>

    </aside>


    {{-- MAIN AREA --}}
    <div class="min-h-screen lg:pl-64">

        {{-- TOP BAR --}}
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">

            <div class="flex h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">

                <div class="flex min-w-0 items-center gap-4">

                    <button type="button"
                            onclick="toggleSidebar()"
                            class="flex h-10 w-10 shrink-0 items-center justify-center
                                   rounded-xl border border-slate-200 text-slate-600
                                   transition hover:bg-slate-100 lg:hidden"
                            aria-label="Open navigation">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-bold tracking-tight text-slate-900 sm:text-xl">
                            @yield('page-title', 'Dashboard')
                        </h1>

                        <p class="mt-1 hidden text-xs text-slate-500 sm:block">
                            Welcome back, {{ auth()->user()->name }}
                        </p>
                    </div>

                </div>

                <div class="flex shrink-0 items-center gap-3">

                    {{-- CURRENT DATE --}}
                    <div class="hidden items-center gap-2 rounded-xl border border-slate-200
                                bg-slate-50 px-3 py-2 text-sm text-slate-600 md:flex">
                        <i class="fa-regular fa-calendar text-slate-400"></i>
                        <span>{{ now()->format('d M Y') }}</span>
                    </div>

                    {{-- USER AVATAR --}}
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl
                                bg-blue-50 font-bold text-blue-700 ring-1 ring-blue-100">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                </div>

            </div>

        </header>


        {{-- PAGE CONTENT --}}
        <main class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

            {{-- BREADCRUMB --}}
            <div class="mb-6 flex items-center gap-2 text-xs text-slate-400">
                <i class="fa-solid fa-house"></i>
                <span>/</span>
                <span class="font-medium text-slate-600">
                    @yield('breadcrumb', 'Workspace')
                </span>
            </div>

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200
                            bg-emerald-50 px-4 py-4 text-sm text-emerald-800">

                    <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>

                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if (session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200
                            bg-red-50 px-4 py-4 text-sm text-red-800">

                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-600"></i>

                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">

                    <div class="mb-2 flex items-center gap-2 font-semibold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Please fix the following errors:
                    </div>

                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif

            {{-- EXISTING PAGE CONTENT --}}
            @yield('content')

        </main>

        {{-- FOOTER --}}
        <footer class="border-t border-slate-200 bg-white px-6 py-4 lg:px-8">
            <div class="mx-auto flex max-w-[1600px] flex-col justify-between gap-2
                        text-xs text-slate-400 sm:flex-row">
                <p>
                    &copy; {{ date('Y') }}
                    {{ \App\Models\Parametre::first()?->store_name ?? 'POS System' }}.
                    All rights reserved.
                </p>

                <p>Point of Sale Management System</p>
            </div>
        </footer>

    </div>

</div>

@endauth

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (!sidebar || !overlay) return;

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>

@livewireScripts

</body>
</html>
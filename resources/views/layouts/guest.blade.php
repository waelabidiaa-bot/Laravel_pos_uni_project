<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">

    <title>@yield('title', 'Login') - {{ \App\Models\Parametre::first()?->store_name ?? 'POS System' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @livewireStyles
</head>

<body class="min-h-screen bg-slate-950 antialiased">

    @yield('content')

    @livewireScripts

</body>

</html>
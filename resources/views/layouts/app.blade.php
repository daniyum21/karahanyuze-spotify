<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Karahanyuze - Rwandan Music Heritage')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-900 text-white min-h-screen">
    @include('components.header')
    
    <main>
        @yield('content')
    </main>

    @include('components.music-player')
</body>
</html>


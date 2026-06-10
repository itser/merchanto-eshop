<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4">
                <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-900">
                    {{ config('app.name') }}
                </a>

                <nav class="flex items-center gap-6 text-sm font-medium">
                    <a href="{{ url('/products') }}" class="text-gray-600 hover:text-gray-900">
                        Products
                    </a>

                    {{-- Cart badge: Livewire component in Phase 4 --}}
                    <span class="text-gray-500">
                        Cart
                    </span>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-8">
            @yield('content')
        </main>

        @stack('scripts')
        @livewireScripts
    </body>
</html>

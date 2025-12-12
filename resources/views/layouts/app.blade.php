<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased h-full text-gray-900">
    <div class="min-h-screen flex flex-col">
        <div class="flex flex-1 bg-gray-50">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col min-h-screen">
                @include('layouts.navigation')

                <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">
                    @hasSection('header')
                        <header class="border-b border-gray-200 pb-4">
                            @yield('header')
                        </header>
                    @endhasSection

                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</body>
</html>

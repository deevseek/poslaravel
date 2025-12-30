<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased h-full text-slate-900">
    <div class="min-h-screen flex flex-col bg-slate-100">
        <div class="flex flex-1 bg-slate-100">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col min-h-screen">
                @include('layouts.navigation')

                <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">
                    @isset($header)
                        <header class="border-b border-slate-200 pb-4">
                            {{ $header }}
                        </header>
                    @elseif(View::hasSection('header'))
                        <header class="border-b border-slate-200 pb-4">
                            @yield('header')
                        </header>
                    @endif

                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </main>
            </div>
        </div>
    </div>
</body>
</html>

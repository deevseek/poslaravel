<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased h-full text-slate-900">
    <div class="min-h-screen bg-slate-950">
        <div class="flex min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col min-h-screen">
                @include('layouts.navigation')

                <main class="flex-1 px-4 pb-10 pt-6 sm:px-6 lg:px-10">
                    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6">
                        @isset($header)
                            <header class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm backdrop-blur">
                                {{ $header }}
                            </header>
                        @elseif(View::hasSection('header'))
                            <header class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm backdrop-blur">
                                @yield('header')
                            </header>
                        @endif

                        <section class="rounded-3xl border border-slate-200/70 bg-white/80 p-5 shadow-sm backdrop-blur">
                            @isset($slot)
                                {{ $slot }}
                            @else
                                @yield('content')
                            @endisset
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>

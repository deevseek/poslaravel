<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full text-gray-900">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white shadow-xl rounded-lg p-8 space-y-6">
            <div class="text-center">
                <x-application-logo class="mx-auto h-12 w-12" />
                <h1 class="mt-3 text-xl font-semibold text-gray-900">POS Dealer Komputer & Service</h1>
                <p class="text-sm text-gray-500">Masuk untuk mulai menggunakan sistem</p>
            </div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>

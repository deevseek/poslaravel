@props(['title' => config('app.name')])

<x-layouts.app :title="$title" :header="$header ?? null">
    {{ $slot }}
</x-layouts.app>

@props([
    'title' => config('app.name'),
    'header' => null,
])

@include('layouts.app', ['title' => $title, 'header' => $header, 'slot' => $slot])

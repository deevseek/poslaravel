@props(['active'])

@php
$classes = $active ?? false
            ? 'block w-full text-left px-4 py-2 text-sm text-gray-700 bg-gray-100'
            : 'block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>

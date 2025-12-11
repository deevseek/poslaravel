@props(['active'])

@php
$classes = $active ?? false
            ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-blue-500 text-left text-base font-medium text-blue-700 bg-blue-50'
            : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

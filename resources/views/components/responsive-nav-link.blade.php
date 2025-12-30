@props(['active'])

@php
$classes = $active ?? false
            ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-left text-base font-semibold text-indigo-700 bg-indigo-50'
            : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

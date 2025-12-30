@props(['active'])

@php
$classes = $active ?? false
            ? 'inline-flex items-center border-b-2 border-indigo-500 px-1 pt-1 text-sm font-semibold text-slate-900'
            : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-600 hover:border-slate-300 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

@props(['active'])

@php
$classes = $active ?? false
            ? 'inline-flex items-center rounded-full bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm'
            : 'inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

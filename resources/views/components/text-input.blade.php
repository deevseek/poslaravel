@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'mt-1 block w-full rounded-lg border-slate-200 bg-white text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100',
]) !!}>

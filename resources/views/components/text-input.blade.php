@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'mt-1 block w-full rounded-xl border-slate-200/80 bg-white/80 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100',
]) !!}>

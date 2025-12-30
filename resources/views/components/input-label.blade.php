@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-slate-700 tracking-wide']) }}>
    {{ $value ?? $slot }}
</label>

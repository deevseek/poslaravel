@props([
    'phone' => null,
    'text' => null,
    'fallback' => '-',
])

@php
    $rawPhone = $phone ?? '';
    $digitsOnly = preg_replace('/\\D+/', '', $rawPhone);
    $url = $digitsOnly !== '' ? "https://wa.me/{$digitsOnly}" : '';
    $label = $text ?? $rawPhone;
@endphp

@if ($url)
    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" {{ $attributes }}>
        {{ $label }}
        <span class="sr-only">Buka WhatsApp</span>
    </a>
@else
    <span {{ $attributes }}>{{ $fallback }}</span>
@endif

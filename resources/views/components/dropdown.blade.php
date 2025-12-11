@props(['align' => 'right', 'width' => '48'])

@php
    $alignmentClasses = [
        'left' => 'origin-top-left left-0',
        'top' => 'origin-top',
        'right' => 'origin-top-right right-0',
    ];

    $widthClasses = [
        '48' => 'w-48',
        '56' => 'w-56',
    ];
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $widthClasses[$width] ?? $width }} rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 {{ $alignmentClasses[$align] ?? $align }}"
        style="display: none;">
        {{ $content }}
    </div>
</div>

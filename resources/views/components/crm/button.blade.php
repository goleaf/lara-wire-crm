@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-sm',
    ];

    $variants = [
        'primary' => 'crm-btn-primary',
        'secondary' => 'crm-btn-secondary',
        'danger' => 'crm-btn-danger',
    ];
@endphp

<button
    {{ $attributes
        ->merge(['type' => $type, 'data-livewire-action' => '1'])
        ->class(['crm-btn', $sizes[$size] ?? $sizes['md'], $variants[$variant] ?? $variants['primary']]) }}
>
    {{ $slot }}
</button>

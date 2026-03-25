@props(['address' => []])

@php
    $line = collect([
        $address['street'] ?? null,
        $address['city'] ?? null,
        $address['state'] ?? null,
        $address['zip'] ?? null,
        $address['country'] ?? null,
    ])->filter()->implode(', ');
@endphp

@if ($line !== '')
    <span>{{ $line }}</span>
@else
    <span class="text-slate-500 dark:text-slate-400">—</span>
@endif

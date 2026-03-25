@props(['contact'])

@php
    $first = mb_substr((string) ($contact->first_name ?? ''), 0, 1);
    $last = mb_substr((string) ($contact->last_name ?? ''), 0, 1);
    $initials = trim(strtoupper($first.$last)) ?: 'C';
@endphp

<span class="inline-flex size-9 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white dark:bg-sky-400 dark:text-slate-950">
    {{ $initials }}
</span>

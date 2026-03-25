@props(['account'])

@php
    $type = $account->type ?? 'Unknown';
    $classes = match ($type) {
        'Customer' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
        'Partner' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        'Prospect' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        'Vendor' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300',
    };
@endphp

<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $classes }}">
    <span>{{ $account->name ?? 'Account' }}</span>
    @if (! empty($account->type))
        <span class="opacity-70">• {{ $account->type }}</span>
    @endif
</span>

@php
    $toneClasses = match ($tone ?? 'sky') {
        'emerald' => 'from-emerald-500/20 to-emerald-500/5 text-emerald-700 dark:text-emerald-300',
        'amber' => 'from-amber-500/20 to-amber-500/5 text-amber-700 dark:text-amber-300',
        'rose' => 'from-rose-500/20 to-rose-500/5 text-rose-700 dark:text-rose-300',
        'violet' => 'from-violet-500/20 to-violet-500/5 text-violet-700 dark:text-violet-300',
        'indigo' => 'from-indigo-500/20 to-indigo-500/5 text-indigo-700 dark:text-indigo-300',
        default => 'from-sky-500/20 to-sky-500/5 text-sky-700 dark:text-sky-300',
    };
@endphp

<article class="rounded-2xl border border-slate-200 bg-gradient-to-br {{ $toneClasses }} bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $value }}</p>
    @if (! empty($trend))
        <p class="mt-2 text-xs font-semibold">{{ $trend }}</p>
    @endif
</article>

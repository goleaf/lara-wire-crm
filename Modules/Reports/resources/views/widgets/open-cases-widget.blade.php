<div>
    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Open Cases</h4>
    <div class="mt-3 space-y-2">
        @forelse ($openCases as $supportCase)
            <a href="{{ route('cases.show', $supportCase->id) }}" wire:navigate class="block rounded-xl border border-slate-200 px-3 py-2 text-sm hover:border-sky-300 dark:border-slate-700">
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ $supportCase->number }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $supportCase->title }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $supportCase->priority }} • {{ $supportCase->status }}</p>
            </a>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No open cases available.</p>
        @endforelse
    </div>
</div>

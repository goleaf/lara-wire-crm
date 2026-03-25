<div>
    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Recent Deals</h4>
    <div class="mt-3 space-y-2">
        @forelse ($recentDeals as $deal)
            <a href="{{ route('deals.show', $deal->id) }}" wire:navigate class="block rounded-xl border border-slate-200 px-3 py-2 text-sm hover:border-sky-300 dark:border-slate-700">
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ $deal->name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    {{ $deal->stage?->name ?? 'No stage' }} • {{ number_format((float) $deal->amount, 2) }} {{ $deal->currency }}
                </p>
            </a>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No deals available.</p>
        @endforelse
    </div>
</div>

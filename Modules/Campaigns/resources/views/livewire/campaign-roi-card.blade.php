<div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Top Campaign ROI</h2>

    <div class="mt-4 space-y-3">
        @forelse ($campaigns as $campaign)
            @php
                $width = round((abs($campaign->roi) / $maxRoi) * 100, 2);
            @endphp
            <div>
                <div class="mb-1 flex justify-between gap-2 text-xs">
                    <span class="truncate text-slate-700 dark:text-slate-300">{{ $campaign->name }}</span>
                    <span class="font-semibold {{ $campaign->roi >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                        {{ number_format($campaign->roi, 2) }}%
                    </span>
                </div>
                <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                    <div class="h-2 rounded-full {{ $campaign->roi >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: {{ $width }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No campaign ROI data yet.</p>
        @endforelse
    </div>
</div>

<div>
    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Pipeline Funnel</h4>
    <div class="mt-4 space-y-2">
        @php
            $max = max(1, (int) $pipelineStages->max());
        @endphp
        @forelse ($pipelineStages as $stage => $count)
            @php
                $width = max(12, round(((int) $count / $max) * 100, 2));
            @endphp
            <div>
                <div class="mb-1 flex items-center justify-between text-xs text-slate-600 dark:text-slate-300">
                    <span>{{ $stage }}</span>
                    <span class="font-semibold">{{ $count }}</span>
                </div>
                <div class="h-3 rounded-full bg-slate-200 dark:bg-slate-700">
                    <div class="h-3 rounded-full bg-gradient-to-r from-sky-500 to-indigo-500" style="width: {{ $width }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No stage data available.</p>
        @endforelse
    </div>
</div>

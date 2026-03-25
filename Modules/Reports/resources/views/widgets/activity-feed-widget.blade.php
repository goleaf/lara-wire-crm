<div>
    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Recent Activities</h4>
    <div class="mt-3 space-y-2">
        @forelse ($recentActivities as $activity)
            <div class="rounded-xl border border-slate-200 px-3 py-2 text-sm dark:border-slate-700">
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ $activity->subject }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $activity->type }} • {{ $activity->status }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400">No activities available.</p>
        @endforelse
    </div>
</div>

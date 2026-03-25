<div class="pointer-events-none fixed right-4 top-4 z-[95] flex w-full max-w-sm flex-col gap-2">
    @foreach ($toasts as $toast)
        @php
            $typeClasses = match ($toast['type']) {
                'success' => 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200',
                'error' => 'border-rose-300 bg-rose-50 text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200',
                'warning' => 'border-amber-300 bg-amber-50 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200',
                default => 'border-sky-300 bg-sky-50 text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-200',
            };
        @endphp
        <div
            x-data
            x-init="setTimeout(() => $wire.dismiss('{{ $toast['id'] }}'), 4000)"
            class="pointer-events-auto rounded-2xl border px-4 py-3 text-sm shadow-lg {{ $typeClasses }}"
        >
            <div class="flex items-start justify-between gap-3">
                <p>{{ $toast['message'] }}</p>
                <button type="button" wire:click="dismiss('{{ $toast['id'] }}')" class="rounded px-1 py-0.5 text-xs font-semibold">✕</button>
            </div>
        </div>
    @endforeach
</div>

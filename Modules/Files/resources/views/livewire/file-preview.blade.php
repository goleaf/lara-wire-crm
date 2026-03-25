<article class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h4 class="text-base font-semibold text-slate-900 dark:text-white">{{ $file->name }}</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $file->original_filename }} • {{ $file->size_formatted }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="previous" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Prev</button>
            <button wire:click="next" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Next</button>
            <a href="{{ route('files.download', $file->id) }}" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Download</a>
        </div>
    </div>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
        @if ($file->is_image)
            <img src="{{ route('files.preview', $file->id) }}" alt="{{ $file->name }}" class="mx-auto max-h-96 rounded-xl object-contain" />
        @elseif ($file->is_pdf)
            <embed src="{{ route('files.preview', $file->id) }}" class="h-96 w-full rounded-xl border border-slate-200 dark:border-slate-700" type="application/pdf" />
        @else
            <div class="text-center text-sm text-slate-500 dark:text-slate-400">
                Preview is not available for this file type.
            </div>
        @endif
    </div>

    @if ($file->versions->isNotEmpty())
        <div class="mt-4">
            <p class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Version history</p>
            <ul class="grid gap-2 text-xs">
                @foreach ($file->versions as $version)
                    <li class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
                        <span class="text-slate-600 dark:text-slate-300">v{{ $version->version }} • {{ $version->created_at?->diffForHumans() }}</span>
                        <a href="{{ route('files.download', $version->id) }}" class="font-medium text-sky-600 dark:text-sky-300">Download</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</article>

<article class="crm-card p-6">
    <h4 class="text-base font-semibold text-slate-900 dark:text-white">Upload Files</h4>

    <form wire:submit="save" class="mt-4 space-y-4">
        <label class="block rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500 transition hover:border-sky-300 hover:bg-sky-50 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-400 dark:hover:border-sky-400/40 dark:hover:bg-sky-500/10">
            <span class="font-medium">Drag and drop files here</span>
            <span class="mt-1 block text-xs">or click to browse</span>
            <input wire:model="uploads" type="file" multiple class="hidden" />
        </label>

        @error('uploads') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
        @error('uploads.*') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

        @if ($uploads !== [])
            <ul class="grid gap-2 text-sm text-slate-600 dark:text-slate-300">
                @foreach ($uploads as $upload)
                    <li class="rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
                        {{ $upload->getClientOriginalName() }} ({{ number_format(($upload->getSize() ?? 0) / 1024, 2) }} KB)
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="flex justify-end">
            <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                Upload
            </button>
        </div>
    </form>
</article>

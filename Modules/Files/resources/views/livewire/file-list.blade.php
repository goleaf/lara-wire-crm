<section class="space-y-4">
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950/40">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Size</th>
                    <th class="px-4 py-3">Uploader</th>
                    <th class="px-4 py-3">Version</th>
                    <th class="px-4 py-3">Uploaded</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($files as $file)
                    <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $file->name }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $file->size_formatted }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $file->uploadedBy?->full_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">v{{ $file->version }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $file->created_at?->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('files.download', $file->id) }}" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Download</a>
                                <a href="{{ route('files.preview', $file->id) }}" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Preview</a>
                                <button wire:click="deleteFile('{{ $file->id }}')" onclick="return confirm('Delete this file?')" class="rounded-lg border border-rose-300 px-2.5 py-1 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No files linked to this record.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $files->links() }}
</section>

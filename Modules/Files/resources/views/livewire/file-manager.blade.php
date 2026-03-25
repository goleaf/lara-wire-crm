<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">File Manager</h3>
            <div class="flex items-center gap-2">
                <button wire:click="setViewMode('grid')" class="rounded-xl px-3 py-2 text-sm font-medium {{ $viewMode === 'grid' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                    Grid
                </button>
                <button wire:click="setViewMode('list')" class="rounded-xl px-3 py-2 text-sm font-medium {{ $viewMode === 'list' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                    List
                </button>
            </div>
        </div>

        <div class="mt-6 grid gap-3 md:grid-cols-6">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search files..."
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm md:col-span-2 dark:border-slate-700 dark:bg-slate-900"
            />

            <select wire:model.live="typeFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All types</option>
                <option value="image">Images</option>
                <option value="pdf">PDF</option>
                <option value="doc">Documents</option>
                <option value="other">Other</option>
            </select>

            <select wire:model.live="uploadedByFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All uploaders</option>
                @foreach ($uploaders as $uploader)
                    <option value="{{ $uploader->id }}">{{ $uploader->full_name }}</option>
                @endforeach
            </select>

            <input wire:model.live="dateFrom" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <input wire:model.live="dateTo" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
        </div>
    </x-crm.card>

    @livewire(\Modules\Files\Livewire\FileUploadZone::class, [], key('files-upload-zone'))

    <x-crm.card class="p-5">
        @if ($viewMode === 'grid')
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($files as $file)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                        <button wire:click="pickFile('{{ $file->id }}')" class="w-full text-left">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $file->is_image ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300' : ($file->is_pdf ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300') }}">
                                    {{ strtoupper($file->extension) }}
                                </span>
                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">v{{ $file->version }}</span>
                            </div>
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $file->name }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $file->size_formatted }}</p>
                        </button>

                        <div class="mt-4 flex items-center gap-2">
                            <a href="{{ route('files.download', $file->id) }}" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Download</a>
                            <button wire:click="startRename('{{ $file->id }}')" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Rename</button>
                            <button wire:click="deleteFile('{{ $file->id }}')" onclick="return confirm('Delete this file?')" class="rounded-lg border border-rose-300 px-2.5 py-1 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300">Delete</button>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full py-8 text-center text-sm text-slate-500 dark:text-slate-400">No files found.</p>
                @endforelse
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3">File</th>
                            <th class="px-4 py-3">Type</th>
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
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ strtoupper($file->extension) }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $file->size_formatted }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $file->uploadedBy?->full_name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">v{{ $file->version }}</td>
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $file->created_at?->diffForHumans() }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('files.preview', $file->id) }}" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Preview</a>
                                        <a href="{{ route('files.download', $file->id) }}" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:text-slate-200">Download</a>
                                        <button wire:click="deleteFile('{{ $file->id }}')" onclick="return confirm('Delete this file?')" class="rounded-lg border border-rose-300 px-2.5 py-1 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No files found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-5">
            {{ $files->links() }}
        </div>
    </x-crm.card>

    @if ($renamingFileId)
        <article class="rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/40 dark:bg-amber-500/10">
            <div class="flex flex-wrap items-center gap-2">
                <input wire:model.live="newName" type="text" class="min-w-[18rem] flex-1 rounded-xl border border-amber-300 bg-white px-3 py-2 text-sm dark:border-amber-500/40 dark:bg-slate-900" />
                <button wire:click="saveRename" class="rounded-xl bg-amber-500 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-400">
                    Save
                </button>
                <button wire:click="$set('renamingFileId', null)" class="rounded-xl border border-amber-300 px-3 py-2 text-sm font-medium text-amber-700 dark:border-amber-500/40 dark:text-amber-300">
                    Cancel
                </button>
            </div>
        </article>
    @endif

    @if ($selectedFileId)
        @livewire(\Modules\Files\Livewire\FilePreview::class, ['fileId' => $selectedFileId], key('preview-'.$selectedFileId))
    @endif
</section>

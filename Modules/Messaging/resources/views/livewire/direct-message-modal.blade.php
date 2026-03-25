<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
            <div class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-5 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Start Direct Message</h3>
                    <button type="button" wire:click="close" class="rounded p-1 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">✕</button>
                </div>

                <div class="space-y-3">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search users..."
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
                    >

                    <div class="max-h-72 space-y-1 overflow-y-auto rounded-md border border-slate-200 p-2 dark:border-slate-700">
                        @forelse ($users as $user)
                            <button
                                type="button"
                                wire:click="startDm('{{ $user->id }}')"
                                class="flex w-full items-center justify-between rounded px-2 py-2 text-left hover:bg-slate-100 dark:hover:bg-slate-800"
                            >
                                <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $user->full_name }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</span>
                            </button>
                        @empty
                            <p class="px-2 py-4 text-center text-xs text-slate-500 dark:text-slate-400">No users match your search.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

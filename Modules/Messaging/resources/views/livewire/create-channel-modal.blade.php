<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
            <div class="w-full max-w-2xl rounded-xl border border-slate-200 bg-white p-5 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Create Channel</h3>
                    <button type="button" wire:click="close" class="rounded p-1 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">✕</button>
                </div>

                <form wire:submit.prevent="create" class="space-y-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="space-y-1">
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Type</span>
                            <select wire:model="type" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                                <option value="Public">Public</option>
                                <option value="Private">Private</option>
                            </select>
                        </label>

                        <label class="space-y-1">
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Name</span>
                            <input wire:model="name" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" placeholder="sales-team">
                        </label>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="space-y-1">
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Related Type (optional)</span>
                            <input wire:model="relatedType" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" placeholder="Modules\Deals\Models\Deal">
                        </label>
                        <label class="space-y-1">
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Related ID (optional)</span>
                            <input wire:model="relatedId" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" placeholder="UUID">
                        </label>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-300">Members</label>
                        <input wire:model.live.debounce.300ms="memberSearch" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" placeholder="Search users...">
                        <div class="max-h-56 space-y-1 overflow-y-auto rounded-md border border-slate-200 p-2 dark:border-slate-700">
                            @forelse ($users as $user)
                                @php $checked = in_array($user->id, $memberIds, true); @endphp
                                <label class="flex items-center justify-between rounded px-2 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800">
                                    <span class="text-sm text-slate-800 dark:text-slate-100">{{ $user->full_name }}</span>
                                    <input type="checkbox" @checked($checked) wire:click="toggleMember('{{ $user->id }}')" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                </label>
                            @empty
                                <p class="px-2 py-4 text-center text-xs text-slate-500 dark:text-slate-400">No users found.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="close" class="rounded-md border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Cancel</button>
                        <button type="submit" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">Create</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/50 p-4 md:items-center">
            <div class="w-full max-w-lg rounded-3xl border border-white/20 bg-white p-5 shadow-xl dark:bg-slate-950">
                <div class="flex items-center justify-between">
                    <h4 class="text-base font-semibold text-slate-900 dark:text-white">Quick Add Activity</h4>
                    <button wire:click="close" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs dark:border-slate-700">Close</button>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="grid gap-3 md:grid-cols-2">
                        <select wire:model.live="type" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                            <option value="Task">Task</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Note">Note</option>
                            <option value="SMS">SMS</option>
                        </select>
                        <select wire:model.live="ownerId" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                            <option value="{{ auth()->id() }}">Me</option>
                        </select>
                    </div>

                    <input wire:model.live.debounce.300ms="subject" type="text" placeholder="Subject" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('subject') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

                    <input wire:model.live="dueDate" type="datetime-local" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('dueDate') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <a href="{{ route('activities.create') }}" wire:navigate class="text-xs font-medium text-sky-600 hover:underline dark:text-sky-300">
                        Open full form
                    </a>
                    <button wire:click="save" class="rounded-xl bg-sky-600 px-4 py-2 text-xs font-semibold text-white">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>

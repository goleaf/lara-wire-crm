<section class="mx-auto max-w-3xl">
    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">
            {{ $roleId ? 'Edit Role' : 'Create Role' }}
        </h3>

        <form wire:submit="save" class="mt-6 space-y-5">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
                <input wire:model.live="name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @foreach (['can_view' => 'Can View', 'can_create' => 'Can Create', 'can_edit' => 'Can Edit', 'can_delete' => 'Can Delete', 'can_export' => 'Can Export'] as $key => $label)
                    <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 dark:border-slate-700">
                        <input wire:model.live="{{ $key }}" type="checkbox" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Record Visibility</label>
                <select wire:model.live="record_visibility" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    <option value="own">Own</option>
                    <option value="team">Team</option>
                    <option value="all">All</option>
                </select>
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Module Access</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($moduleAccessOptions as $moduleKey)
                        <label class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 dark:border-slate-700">
                            <span class="text-sm text-slate-700 capitalize dark:text-slate-300">{{ $moduleKey }}</span>
                            <input wire:model.live="module_access.{{ $moduleKey }}" type="checkbox" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('roles.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                    Cancel
                </a>
                <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                    Save Role
                </button>
            </div>
        </form>
    </article>
</section>


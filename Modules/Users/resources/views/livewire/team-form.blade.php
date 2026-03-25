<section class="mx-auto max-w-3xl">
    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">
            {{ $teamId ? 'Edit Team' : 'Create Team' }}
        </h3>

        <form wire:submit="save" class="mt-6 space-y-5">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
                <input wire:model.live="name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Manager</label>
                <select wire:model.live="manager_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    <option value="">No manager</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('manager_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Region</label>
                <input wire:model.live="region" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                @error('region') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Members</label>
                <select wire:model.live="member_ids" multiple size="8" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('member_ids') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('teams.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                    Cancel
                </a>
                <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                    Save Team
                </button>
            </div>
        </form>
    </article>
</section>


<section class="mx-auto max-w-3xl space-y-6">
    <x-crm.status />

    <article class="crm-card p-6">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">
            {{ $userId ? 'Edit User' : 'Create User' }}
        </h3>

        <form wire:submit="save" class="mt-6 space-y-5">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Full Name</label>
                    <input wire:model.live="full_name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('full_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                    <input wire:model.live="email" type="email" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ $userId ? 'Password (optional)' : 'Password' }}
                    </label>
                    <input wire:model.live="password" type="password" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Role</label>
                    <select wire:model.live="role_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Select role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Team</label>
                    <select wire:model.live="team_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">No team</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @error('team_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Quota</label>
                    <input wire:model.live="quota" type="number" min="0" step="0.01" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('quota') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Avatar</label>
                    <input wire:model="avatar" type="file" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('avatar') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input wire:model.live="is_active" id="is_active" type="checkbox" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                <label for="is_active" class="text-sm text-slate-700 dark:text-slate-300">Active account</label>
            </div>

            @if ($existingAvatarPath && ! $avatar)
                <img src="{{ asset('storage/'.$existingAvatarPath) }}" alt="Current Avatar" class="size-14 rounded-full object-cover" />
            @endif

            @if ($avatar)
                <img src="{{ $avatar->temporaryUrl() }}" alt="Preview" class="size-14 rounded-full object-cover" />
            @endif

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                    Cancel
                </a>
                <button type="submit" class="crm-btn crm-btn-primary">
                    Save User
                </button>
            </div>
        </form>
    </article>
</section>


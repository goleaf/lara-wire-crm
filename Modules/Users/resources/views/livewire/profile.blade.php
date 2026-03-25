<section class="mx-auto max-w-4xl space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">My Profile</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update account details, notification preferences, and password.</p>
            </div>
            @if ($avatar_path)
                <img src="{{ asset('storage/'.$avatar_path) }}" alt="Avatar" class="size-16 rounded-2xl object-cover" />
            @endif
        </div>
    </x-crm.card>

    <form wire:submit="save" class="space-y-6">
        <x-crm.card class="p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Account</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Full Name</span>
                    <input wire:model.live.debounce.300ms="full_name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('full_name') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Email</span>
                    <input wire:model.live.debounce.300ms="email" type="email" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('email') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Avatar</span>
                    <input wire:model="avatar" type="file" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('avatar') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
            </div>
        </x-crm.card>

        <x-crm.card class="p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Security</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Current Password</span>
                    <input wire:model.live="current_password" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('current_password') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
                <div></div>
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">New Password</span>
                    <input wire:model.live="password" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('password') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Confirm Password</span>
                    <input wire:model.live="password_confirmation" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </label>
            </div>
        </x-crm.card>

        <x-crm.card class="p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Notification Preferences</h3>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @foreach ($notification_types as $type => $enabled)
                    <label class="inline-flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $type }}</span>
                        <input wire:model.live="notification_types.{{ $type }}" type="checkbox" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                    </label>
                @endforeach
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Quiet Hours Start</span>
                    <input wire:model.live="quiet_hours_start" type="time" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('quiet_hours_start') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Quiet Hours End</span>
                    <input wire:model.live="quiet_hours_end" type="time" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('quiet_hours_end') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </label>
            </div>
        </x-crm.card>

        <x-crm.card class="p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Quota Progress (This Month)</h3>
                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-200">
                        {{ number_format($quota_used, 2) }} / {{ number_format($quota, 2) }}
                    </p>
                </div>
                <div class="w-48">
                    <div class="h-3 rounded-full bg-slate-200 dark:bg-slate-800">
                        <div class="h-3 rounded-full bg-sky-600" style="width: {{ $quotaPercent }}%"></div>
                    </div>
                    <p class="mt-1 text-right text-xs text-slate-500 dark:text-slate-400">{{ $quotaPercent }}%</p>
                </div>
            </div>
        </x-crm.card>

        <div class="flex justify-end">
            <button type="submit" class="crm-btn crm-btn-primary">
                Save Profile
            </button>
        </div>
    </form>
</section>

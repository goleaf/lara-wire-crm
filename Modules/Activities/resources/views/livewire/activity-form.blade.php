<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $activityId ? 'Edit Activity' : 'New Activity' }}</h3>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Type</label>
                    <select wire:model.live="type" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($types as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</label>
                    <input wire:model.live.debounce.300ms="subject" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('subject') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Description</label>
                    <textarea wire:model.live.debounce.300ms="description" rows="4" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                    @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                    <select wire:model.live="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($statuses as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</label>
                    <select wire:model.live="priority" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($priorities as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('priority') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Due Date</label>
                    <input wire:model.live="dueDate" type="datetime-local" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('dueDate') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Duration (minutes)</label>
                    <input wire:model.live="durationMinutes" type="number" min="1" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <div class="mt-2 flex flex-wrap gap-2">
                        <button type="button" wire:click="applyDurationPreset(15)" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs dark:border-slate-700">15m</button>
                        <button type="button" wire:click="applyDurationPreset(30)" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs dark:border-slate-700">30m</button>
                        <button type="button" wire:click="applyDurationPreset(60)" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs dark:border-slate-700">1h</button>
                        <button type="button" wire:click="applyDurationPreset(120)" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs dark:border-slate-700">2h</button>
                    </div>
                    @error('durationMinutes') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Related Type</label>
                    <select wire:model.live="relatedType" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">No linked record</option>
                        @foreach ($relatedTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('relatedType') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Related Search</label>
                    <input wire:model.live.debounce.300ms="relatedSearch" type="text" placeholder="Search related records" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Related Record</label>
                    <select wire:model.live="relatedId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Choose record</option>
                        @foreach ($relatedRecords as $record)
                            <option value="{{ $record['id'] }}">{{ $record['label'] }}</option>
                        @endforeach
                    </select>
                    @error('relatedId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Owner</label>
                    <select wire:model.live="ownerId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                        @endforeach
                    </select>
                    @error('ownerId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Reminder At</label>
                    <input wire:model.live="reminderAt" type="datetime-local" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('reminderAt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Attendees</label>
                    <select wire:model.live="attendeeIds" multiple size="5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                        @endforeach
                    </select>
                    @error('attendeeIds') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    @error('attendeeIds.*') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                @if ($status === 'Completed')
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Outcome</label>
                        <textarea wire:model.live.debounce.300ms="outcome" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                        @error('outcome') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>
        </article>

        <div class="flex justify-end gap-2">
            <a href="{{ $activityId ? route('activities.show', $activityId) : route('activities.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-sky-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                Save Activity
            </button>
        </div>
    </form>
</section>

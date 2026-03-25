<form wire:submit="save" class="space-y-4">
    <div class="grid gap-3 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Title</label>
            <input wire:model.live.debounce.300ms="title" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Type</label>
            <select wire:model.live="type" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="Meeting">Meeting</option>
                <option value="Demo">Demo</option>
                <option value="Follow-up">Follow-up</option>
                <option value="Reminder">Reminder</option>
                <option value="Other">Other</option>
            </select>
            @error('type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                <input wire:model.live="allDay" type="checkbox" />
                <span>All day</span>
            </label>
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $allDay ? 'Start Date' : 'Start At' }}</label>
            <input wire:model.live="startAt" type="{{ $allDay ? 'date' : 'datetime-local' }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            @error('startAt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        @if (! $allDay)
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End At</label>
                <input wire:model.live="endAt" type="datetime-local" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                @error('endAt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Location</label>
            <input wire:model.live.debounce.300ms="location" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            @error('location') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Organizer</label>
            <select wire:model.live="organizerId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                @endforeach
            </select>
            @error('organizerId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Description</label>
            <textarea wire:model.live.debounce.300ms="description" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
            @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Contact</label>
            <input wire:model.live.debounce.300ms="contactSearch" type="text" placeholder="Search contact" class="mb-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <select wire:model.live="contactId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">None</option>
                @foreach ($contacts as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->first_name }} {{ $contact->last_name }}</option>
                @endforeach
            </select>
            @error('contactId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Deal</label>
            <input wire:model.live.debounce.300ms="dealSearch" type="text" placeholder="Search deal" class="mb-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <select wire:model.live="dealId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">None</option>
                @foreach ($deals as $deal)
                    <option value="{{ $deal->id }}">{{ $deal->name }}</option>
                @endforeach
            </select>
            @error('dealId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Reminder</label>
            <select wire:model.live="reminderMinutes" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">None</option>
                <option value="5">5 min</option>
                <option value="15">15 min</option>
                <option value="30">30 min</option>
                <option value="60">1 hour</option>
                <option value="1440">1 day</option>
            </select>
            @error('reminderMinutes') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Recurrence</label>
            <select wire:model.live="recurrence" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="None">None</option>
                <option value="Daily">Daily</option>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
            </select>
            @error('recurrence') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        @if ($recurrence !== 'None')
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Recurrence End Date</label>
                <input wire:model.live="recurrenceEndDate" type="date" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                @error('recurrenceEndDate') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
            <select wire:model.live="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="Scheduled">Scheduled</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
            @error('status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Attendees</label>
            <select wire:model.live="attendeeIds" multiple size="5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                @endforeach
            </select>
            @error('attendeeIds.*') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Color</label>
            <div class="flex flex-wrap gap-2">
                @foreach ($colors as $preset)
                    <button type="button" wire:click="$set('color', '{{ $preset }}')" class="h-8 w-8 rounded-full border-2 {{ $color === $preset ? 'border-slate-900 dark:border-white' : 'border-transparent' }}" style="background-color: {{ $preset }}"></button>
                @endforeach
            </div>
            <input wire:model.live="color" type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 font-mono text-sm dark:border-slate-700 dark:bg-slate-900" />
            @error('color') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white">Save Event</button>
    </div>
</form>

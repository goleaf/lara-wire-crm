<section class="space-y-6">
    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $caseId ? 'Edit Case' : 'Create Case' }}</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Capture support details and SLA context.</p>
    </article>

    <form wire:submit="save" class="space-y-6">
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Title</span>
                    <input wire:model.live.debounce.300ms="title" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @error('title')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Description</span>
                    <textarea wire:model.live.debounce.300ms="description" rows="5" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                    @error('description')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </article>

        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <div class="grid gap-4 md:grid-cols-3">
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Status</span>
                    <select wire:model.live="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <div class="space-y-2">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Priority</span>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($priorities as $priorityOption)
                            @php
                                $priorityClass = match ($priorityOption) {
                                    'Critical' => 'border-rose-300 text-rose-700 dark:border-rose-500/40 dark:text-rose-200',
                                    'High' => 'border-orange-300 text-orange-700 dark:border-orange-500/40 dark:text-orange-200',
                                    'Medium' => 'border-amber-300 text-amber-700 dark:border-amber-500/40 dark:text-amber-200',
                                    default => 'border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-200',
                                };
                            @endphp
                            <button
                                type="button"
                                wire:click="$set('priority', '{{ $priorityOption }}')"
                                class="rounded-xl border px-3 py-2 text-xs font-semibold {{ $priority === $priorityOption ? 'bg-slate-900 text-white dark:bg-sky-400 dark:text-slate-950' : $priorityClass }}"
                            >
                                {{ $priorityOption }}
                            </button>
                        @endforeach
                    </div>
                    @error('priority')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </div>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Type</span>
                    <select wire:model.live="type" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($types as $typeOption)
                            <option value="{{ $typeOption }}">{{ $typeOption }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Account</span>
                    <select wire:model.live="account_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Select account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    @error('account_id')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Contact</span>
                    <select wire:model.live="contact_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Select contact</option>
                        @foreach ($contacts as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->full_name }}</option>
                        @endforeach
                    </select>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400">Selecting a contact can auto-fill account.</span>
                    @error('contact_id')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Deal</span>
                    <select wire:model.live="deal_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Select deal</option>
                        @foreach ($deals as $deal)
                            <option value="{{ $deal->id }}">{{ $deal->name }}</option>
                        @endforeach
                    </select>
                    @error('deal_id')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Owner</span>
                    <select wire:model.live="owner_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                        @endforeach
                    </select>
                    @error('owner_id')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <div class="space-y-2">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Channel</span>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($channels as $channelOption)
                            <button
                                type="button"
                                wire:click="$set('channel', '{{ $channelOption }}')"
                                class="rounded-xl border px-3 py-2 text-xs font-semibold {{ $channel === $channelOption ? 'bg-slate-900 text-white dark:bg-sky-400 dark:text-slate-950' : 'border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}"
                            >
                                {{ $channelOption }}
                            </button>
                        @endforeach
                    </div>
                    @error('channel')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </article>

        <article class="rounded-3xl border border-sky-200 bg-sky-50/80 p-4 shadow-sm dark:border-sky-500/30 dark:bg-sky-500/10">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700 dark:text-sky-300">SLA Preview</p>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-200">
                {{ $sla_preview ? 'Based on current priority, SLA deadline will be around '.$sla_preview : 'No active SLA policy found for this priority.' }}
            </p>
        </article>

        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <label class="space-y-1">
                <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Resolution Notes</span>
                <textarea wire:model.live.debounce.300ms="resolution_notes" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                @error('resolution_notes')
                    <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                @enderror
            </label>
        </article>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('cases.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-500">
                Save Case
            </button>
        </div>
    </form>
</section>

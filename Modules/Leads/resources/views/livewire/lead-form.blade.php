<section class="space-y-6">
    <x-crm.status />

    <form wire:submit="save" class="space-y-6">
        <x-crm.card class="p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Lead Profile</h3>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">First Name</label>
                    <input wire:model.blur="firstName" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('firstName') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Last Name</label>
                    <input wire:model.blur="lastName" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('lastName') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Company</label>
                    <input wire:model.blur="company" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                    <input wire:model.blur="email" type="email" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</label>
                    <input wire:model.blur="phone" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Lead Source</label>
                    <select wire:model.live="leadSource" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($leadSources as $source)
                            <option value="{{ $source }}">{{ $source }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                    <select wire:model.live="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                        @endforeach
                    </select>
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
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Campaign</label>
                    <select wire:model.live="campaignId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">No campaign</option>
                        @foreach ($campaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Auto Score</label>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        {{ $score }}/100
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Based on source, status, campaign, and contactability rules.</p>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Rating</label>
                    <div class="flex gap-2">
                        @foreach ($ratings as $ratingOption)
                            @php
                                $ratingClass = match ($ratingOption) {
                                    'Hot' => 'border-rose-300 text-rose-700 dark:border-rose-500/30 dark:text-rose-300',
                                    'Warm' => 'border-amber-300 text-amber-700 dark:border-amber-500/30 dark:text-amber-300',
                                    default => 'border-blue-300 text-blue-700 dark:border-blue-500/30 dark:text-blue-300',
                                };
                            @endphp
                            <button
                                type="button"
                                wire:click="setRating('{{ $ratingOption }}')"
                                class="rounded-xl border px-4 py-2 text-sm font-semibold {{ $rating === $ratingOption ? 'bg-slate-900 text-white dark:bg-sky-500 dark:text-slate-950' : $ratingClass }}"
                            >
                                {{ $ratingOption }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Description</label>
                    <textarea wire:model.blur="description" rows="4" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                </div>
            </div>
        </x-crm.card>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('leads.index') }}" wire:navigate class="crm-btn crm-btn-secondary">
                Cancel
            </a>
            <button type="submit" class="crm-btn crm-btn-primary">
                Save Lead
            </button>
        </div>
    </form>
</section>

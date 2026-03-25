<div class="space-y-6" x-data="{ tab: 'overview' }">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $campaign->name }}</h1>
            <div class="mt-2 flex items-center gap-2 text-xs">
                <span class="rounded bg-slate-100 px-2 py-0.5 font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">{{ $campaign->type }}</span>
                @php
                    $statusClasses = match ($campaign->status) {
                        'Active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
                        'Completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
                        'Paused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
                        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                    };
                @endphp
                <span class="rounded px-2 py-0.5 font-medium {{ $statusClasses }}">{{ $campaign->status }}</span>
                <span class="text-slate-500 dark:text-slate-400">
                    {{ $campaign->start_date?->toDateString() ?? '—' }} → {{ $campaign->end_date?->toDateString() ?? '—' }}
                </span>
            </div>
        </div>
        <a href="{{ route('campaigns.edit', $campaign->id) }}" wire:navigate class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Edit</a>
    </div>

    <div class="grid gap-3 md:grid-cols-7">
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] uppercase text-slate-500 dark:text-slate-400">Expected Leads</p>
            <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $campaign->expected_leads }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] uppercase text-slate-500 dark:text-slate-400">Actual Leads</p>
            <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $campaign->actual_leads }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] uppercase text-slate-500 dark:text-slate-400">Budget</p>
            <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ number_format((float) $campaign->budget, 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] uppercase text-slate-500 dark:text-slate-400">Actual Cost</p>
            <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ number_format((float) $campaign->actual_cost, 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] uppercase text-slate-500 dark:text-slate-400">Revenue</p>
            <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ number_format($campaign->revenue_generated, 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] uppercase text-slate-500 dark:text-slate-400">ROI</p>
            <p class="mt-1 text-lg font-semibold {{ $campaign->roi >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">{{ number_format($campaign->roi, 2) }}%</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] uppercase text-slate-500 dark:text-slate-400">Conversion</p>
            <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ number_format($campaign->lead_conversion_rate, 2) }}%</p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-2 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <nav class="flex flex-wrap gap-2">
            <button type="button" @click="tab = 'overview'" :class="tab === 'overview' ? 'bg-sky-600 text-white' : 'text-slate-600 dark:text-slate-300'" class="rounded-md px-3 py-2 text-sm">Overview</button>
            <button type="button" @click="tab = 'contacts'" :class="tab === 'contacts' ? 'bg-sky-600 text-white' : 'text-slate-600 dark:text-slate-300'" class="rounded-md px-3 py-2 text-sm">Contacts</button>
            <button type="button" @click="tab = 'leads'" :class="tab === 'leads' ? 'bg-sky-600 text-white' : 'text-slate-600 dark:text-slate-300'" class="rounded-md px-3 py-2 text-sm">Leads</button>
            <button type="button" @click="tab = 'activities'" :class="tab === 'activities' ? 'bg-sky-600 text-white' : 'text-slate-600 dark:text-slate-300'" class="rounded-md px-3 py-2 text-sm">Activities</button>
            <button type="button" @click="tab = 'notes'" :class="tab === 'notes' ? 'bg-sky-600 text-white' : 'text-slate-600 dark:text-slate-300'" class="rounded-md px-3 py-2 text-sm">Notes</button>
        </nav>
    </div>

    <section x-show="tab === 'overview'" x-cloak class="space-y-4">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Description</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $campaign->description ?: 'No description provided.' }}</p>
                <h3 class="mt-4 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Target Audience</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $campaign->target_audience ?: 'Not set.' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                @php
                    $budgetPct = (float) $campaign->budget > 0 ? min(100, round(((float) $campaign->actual_cost / (float) $campaign->budget) * 100, 2)) : 0;
                @endphp
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Budget vs Actual</h2>
                <div class="mt-3 h-3 rounded-full bg-slate-200 dark:bg-slate-700">
                    <div class="h-3 rounded-full {{ (float) $campaign->actual_cost > (float) $campaign->budget ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $budgetPct }}%"></div>
                </div>
                <p class="mt-2 text-sm {{ (float) $campaign->actual_cost > (float) $campaign->budget ? 'text-rose-700 dark:text-rose-300' : 'text-emerald-700 dark:text-emerald-300' }}">
                    {{ number_format((float) $campaign->actual_cost, 2) }} / {{ number_format((float) $campaign->budget, 2) }}
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Lead Funnel</h2>
            @php
                $targeted = $contactStatusCounts['Targeted'] ?? 0;
                $contacted = $contactStatusCounts['Contacted'] ?? 0;
                $responded = $contactStatusCounts['Responded'] ?? 0;
                $converted = $contactStatusCounts['Converted'] ?? 0;
                $maxFunnel = max(1, $targeted, $contacted, $responded, $converted);
                $funnel = [
                    ['label' => 'Targeted', 'value' => $targeted, 'color' => 'bg-slate-500'],
                    ['label' => 'Contacted', 'value' => $contacted, 'color' => 'bg-blue-500'],
                    ['label' => 'Responded', 'value' => $responded, 'color' => 'bg-amber-500'],
                    ['label' => 'Converted', 'value' => $converted, 'color' => 'bg-emerald-500'],
                ];
            @endphp
            <div class="mt-3 space-y-2">
                @foreach ($funnel as $step)
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>{{ $step['label'] }}</span>
                            <span>{{ $step['value'] }}</span>
                        </div>
                        <div class="h-3 rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-3 rounded-full {{ $step['color'] }}" style="width: {{ round(($step['value'] / $maxFunnel) * 100, 2) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section x-show="tab === 'contacts'" x-cloak class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Add Contacts</h2>
            <div class="mt-3 grid gap-3 md:grid-cols-2">
                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Select contacts</span>
                    <select wire:model="selectedContactIds" multiple class="h-32 w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($availableContacts as $contact)
                            <option value="{{ $contact->id }}">{{ trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')) }} ({{ $contact->email ?? 'no-email' }})</option>
                        @endforeach
                    </select>
                </label>
                <div class="md:col-span-2 flex justify-end">
                    <button type="button" wire:click="addContacts" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">Add Contacts</button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Contact</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($campaign->contacts as $contact)
                        <tr>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')) }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $contact->email ?? '—' }}</td>
                            <td class="px-3 py-2">
                                <select wire:change="updateContactStatus('{{ $contact->id }}', $event.target.value)" class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900">
                                    @foreach ($contactStatuses as $status)
                                        <option value="{{ $status }}" @selected((string) $contact->pivot->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button" wire:click="removeContact('{{ $contact->id }}')" class="rounded border border-rose-300 px-2 py-1 text-xs text-rose-700 dark:border-rose-500/40 dark:text-rose-300">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No contacts attached.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section x-show="tab === 'leads'" x-cloak>
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Lead</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Score</th>
                        <th class="px-3 py-2">Converted</th>
                        <th class="px-3 py-2">Deal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($campaign->leads as $lead)
                        <tr>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">
                                @if (Route::has('leads.show'))
                                    <a href="{{ route('leads.show', $lead->id) }}" wire:navigate class="underline">{{ trim($lead->first_name.' '.$lead->last_name) }}</a>
                                @else
                                    {{ trim($lead->first_name.' '.$lead->last_name) }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $lead->status }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $lead->score }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $lead->converted ? 'Yes' : 'No' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">
                                @if ($lead->converted_to_deal_id && Route::has('deals.show'))
                                    <a href="{{ route('deals.show', $lead->converted_to_deal_id) }}" wire:navigate class="underline">{{ $lead->converted_to_deal_id }}</a>
                                @else
                                    {{ $lead->converted_to_deal_id ?: '—' }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No leads linked to this campaign.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section x-show="tab === 'activities'" x-cloak>
        <article class="crm-card p-6">
            @livewire(\Modules\Core\Livewire\ActivityTimeline::class, ['modelType' => $campaign::class, 'modelId' => (string) $campaign->id], key('campaign-timeline-'.$campaign->id))
        </article>
    </section>

    <section x-show="tab === 'notes'" x-cloak>
        <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
            {{ $campaign->description ?: 'No additional notes available.' }}
        </div>
    </section>
</div>

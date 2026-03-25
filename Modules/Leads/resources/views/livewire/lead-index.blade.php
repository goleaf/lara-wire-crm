<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Leads</h3>
            <div class="flex items-center gap-2">
                <x-crm.link-button href="{{ route('leads.kanban') }}" wire:navigate variant="secondary" size="sm">
                    Kanban
                </x-crm.link-button>
                <x-crm.button wire:click="exportCsv" variant="secondary" size="sm">
                    Export CSV
                </x-crm.button>
                <x-crm.link-button href="{{ route('leads.create') }}" wire:navigate variant="primary">
                    New Lead
                </x-crm.link-button>
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-7">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search leads" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />

            <select wire:model.live="statusFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>

            <select wire:model.live="ratingFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All ratings</option>
                @foreach ($ratings as $rating)
                    <option value="{{ $rating }}">{{ $rating }}</option>
                @endforeach
            </select>

            <select wire:model.live="ownerFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All owners</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="campaignFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">Any campaign</option>
                @foreach ($campaigns as $campaign)
                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="leadSourceFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All sources</option>
                @foreach ($leadSources as $source)
                    <option value="{{ $source }}">{{ $source }}</option>
                @endforeach
            </select>

            <select wire:model.live="convertedFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">Any conversion</option>
                <option value="0">Not converted</option>
                <option value="1">Converted</option>
            </select>
        </div>
    </x-crm.card>

    <x-crm.card class="overflow-hidden">
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-3 dark:border-slate-800">
            <select wire:model.live="bulkOwnerId" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-700 dark:bg-slate-900">
                <option value="">Assign owner</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
            <button wire:click="bulkAssignOwner" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Apply Owner</button>

            <select wire:model.live="bulkStatus" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs dark:border-slate-700 dark:bg-slate-900">
                <option value="">Change status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
            <button wire:click="bulkChangeStatus" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Apply Status</button>

            <button wire:click="bulkDelete" onclick="return confirm('Delete selected leads?')" class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300">
                Delete Selected
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Select</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Company</th>
                        <th class="px-4 py-3">Source</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Score</th>
                        <th class="px-4 py-3">Rating</th>
                        <th class="px-4 py-3">Owner</th>
                        <th class="px-4 py-3">Campaign</th>
                        <th class="px-4 py-3">Created</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($leads as $lead)
                        @php
                            $statusClass = match ($lead->status) {
                                'New' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300',
                                'Contacted' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                'Qualified' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                                'Unqualified' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                                default => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
                            };
                            $ratingClass = match ($lead->rating) {
                                'Hot' => 'text-rose-600 dark:text-rose-300',
                                'Warm' => 'text-amber-600 dark:text-amber-300',
                                default => 'text-blue-600 dark:text-blue-300',
                            };
                            $scoreClass = $lead->score <= 30
                                ? 'bg-rose-500'
                                : ($lead->score <= 60 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <tr class="{{ $lead->converted ? 'opacity-70 line-through' : '' }} odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:click="toggleSelection('{{ $lead->id }}')" @checked(in_array($lead->id, $selected, true)) />
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('leads.show', $lead->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                    {{ $lead->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $lead->company ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $lead->lead_source }}</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $lead->status }}</span></td>
                            <td class="px-4 py-3">
                                <div class="w-24 rounded-full bg-slate-200 dark:bg-slate-800">
                                    <div class="h-2 rounded-full {{ $scoreClass }}" style="width: {{ $lead->score }}%"></div>
                                </div>
                                <span class="mt-1 inline-block text-xs text-slate-500">{{ $lead->score }}/100</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold {{ $ratingClass }}">{{ $lead->rating }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $lead->owner?->full_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $lead->campaign?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $lead->created_at?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('leads.edit', $lead->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                        Edit
                                    </a>
                                    @if (! $lead->converted)
                                        <button wire:click="convertLead('{{ $lead->id }}')" class="rounded-lg border border-emerald-300 px-3 py-1.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/40 dark:text-emerald-300">
                                            Convert
                                        </button>
                                    @endif
                                    <button wire:click="deleteLead('{{ $lead->id }}')" onclick="return confirm('Delete this lead?')" class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                                No leads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
            {{ $leads->links() }}
        </div>
    </x-crm.card>
</section>

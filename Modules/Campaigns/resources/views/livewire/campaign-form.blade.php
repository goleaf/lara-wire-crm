<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $campaignId ? 'Edit Campaign' : 'Create Campaign' }}</h1>
        <a href="{{ route('campaigns.index') }}" wire:navigate class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Back</a>
    </div>

    <div class="grid gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-3 dark:border-slate-800 dark:bg-slate-900">
        <label class="space-y-1 md:col-span-2">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Campaign Name</span>
            <input wire:model="name" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('name') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Owner</span>
            <select wire:model="owner_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
            @error('owner_id') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Type</span>
            <select wire:model="type" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($types as $typeOption)
                    <option value="{{ $typeOption }}">{{ $typeOption }}</option>
                @endforeach
            </select>
            @error('type') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Status</span>
            <select wire:model="status" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                @endforeach
            </select>
            @error('status') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Expected Leads</span>
            <input wire:model="expected_leads" type="number" min="0" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('expected_leads') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Start Date</span>
            <input wire:model="start_date" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('start_date') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">End Date</span>
            <input wire:model="end_date" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('end_date') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Budget</span>
            <input wire:model="budget" type="number" min="0" step="0.01" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('budget') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Actual Cost</span>
            <input wire:model="actual_cost" type="number" min="0" step="0.01" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('actual_cost') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1 md:col-span-3">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Target Audience</span>
            <textarea wire:model="target_audience" rows="2" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
            @error('target_audience') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1 md:col-span-3">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Description</span>
            <textarea wire:model="description" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
            @error('description') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>
    </div>

    <div class="flex justify-end">
        <button type="button" wire:click="save" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
            Save Campaign
        </button>
    </div>
</div>

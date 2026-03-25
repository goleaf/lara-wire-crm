<section class="space-y-6">
    <x-crm.status />

    <form wire:submit="save" class="space-y-6">
        <x-crm.card class="p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Deal Info</h3>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Name</label>
                    <input wire:model.blur="name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Account</label>
                    <select wire:model.live="accountId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Select account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    @error('accountId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Contact</label>
                    <select wire:model.live="contactId" wire:key="contact-{{ $accountId }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">No contact</option>
                        @foreach ($contacts as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->full_name }}</option>
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
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pipeline</label>
                    <select wire:model.live="pipelineId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($pipelines as $pipeline)
                            <option value="{{ $pipeline->id }}">{{ $pipeline->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Stage</label>
                    <select wire:model.live="stageId" wire:key="stage-{{ $pipelineId }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->name }} ({{ $stage->probability }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</label>
                    <input wire:model.live="amount" type="number" step="0.01" min="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Probability</label>
                    <input wire:model.live="probability" type="number" min="0" max="100" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Expected Revenue</label>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        {{ number_format($expectedRevenue, 2) }}
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Close Date</label>
                    <input wire:model.live="closeDate" type="date" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Deal Type</label>
                    <select wire:model.live="dealType" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="New Business">New Business</option>
                        <option value="Renewal">Renewal</option>
                        <option value="Upsell">Upsell</option>
                        <option value="Cross-sell">Cross-sell</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Source</label>
                    <input wire:model.blur="source" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>
            </div>
        </x-crm.card>

        <x-crm.card class="p-6">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Products</h3>
                <button type="button" wire:click="addLineItem" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Add Row</button>
            </div>

            <div class="mt-4 space-y-3">
                @foreach ($lineItems as $index => $item)
                    <div class="grid gap-2 rounded-2xl border border-slate-200 p-3 md:grid-cols-6 dark:border-slate-700">
                        <select wire:model.live="lineItems.{{ $index }}.product_id" class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900">
                            <option value="">Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input wire:model.live="lineItems.{{ $index }}.quantity" type="number" min="1" class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900" />
                        <input wire:model.live="lineItems.{{ $index }}.unit_price" type="number" step="0.01" min="0" class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900" />
                        <input wire:model.live="lineItems.{{ $index }}.discount" type="number" step="0.01" min="0" max="100" class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900" />
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                            {{ number_format((float) $item['total'], 2) }}
                        </div>
                        <button type="button" wire:click="removeLineItem({{ $index }})" class="rounded-lg border border-rose-300 px-2 py-1 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300">Remove</button>
                    </div>
                @endforeach
            </div>
        </x-crm.card>

        <div class="flex items-center justify-end gap-2">
            <x-crm.link-button href="{{ route('deals.index') }}" wire:navigate variant="secondary">Cancel</x-crm.link-button>
            <x-crm.button type="submit" variant="primary">Save Deal</x-crm.button>
        </div>
    </form>
</section>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $quoteId ? 'Edit Quote' : 'Create Quote' }}</h1>
        <a href="{{ route('quotes.index') }}" wire:navigate class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Back</a>
    </div>

    <div class="grid gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-3 dark:border-slate-800 dark:bg-slate-900">
        <label class="space-y-1 md:col-span-2">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Quote Name</span>
            <input wire:model="name" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Valid Until</span>
            <input wire:model="valid_until" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Deal</span>
            <select wire:model="deal_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">None</option>
                @foreach ($deals as $deal)
                    <option value="{{ $deal->id }}">{{ $deal->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Contact</span>
            <select wire:model="contact_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">None</option>
                @foreach ($contacts as $contact)
                    <option value="{{ $contact->id }}">{{ trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')) }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Account</span>
            <select wire:model="account_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">None</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Owner</span>
            <select wire:model="owner_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Currency</span>
            <input wire:model="currency" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Status</span>
            <select wire:model="status" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="Draft">Draft</option>
                <option value="Sent">Sent</option>
                <option value="Accepted">Accepted</option>
                <option value="Rejected">Rejected</option>
                <option value="Expired">Expired</option>
            </select>
        </label>

        <label class="space-y-1 md:col-span-3">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Internal Notes</span>
            <textarea wire:model="internal_notes" rows="2" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
        </label>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-3 text-sm font-semibold text-slate-800 dark:text-slate-100">Line Items</h2>
        @livewire(\Modules\Products\Livewire\ProductLineItem::class, ['lineItems' => $lineItems, 'editable' => true], key('quote-line-items-'.($quoteId ?? 'new')))
    </div>

    <div class="grid gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-2 dark:border-slate-800 dark:bg-slate-900">
        <div class="space-y-3">
            <label class="space-y-1">
                <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Discount Type</span>
                <select wire:model.live="discount_type" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    <option value="Percentage">Percentage</option>
                    <option value="Fixed">Fixed</option>
                </select>
            </label>
            <label class="space-y-1">
                <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Discount Value</span>
                <input wire:model.live="discount_value" type="number" min="0" step="0.01" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            </label>
        </div>

        <div class="rounded-lg bg-slate-50 p-4 text-sm dark:bg-slate-800/60">
            <dl class="space-y-2">
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 dark:text-slate-400">Subtotal</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 dark:text-slate-400">Discount</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100">-{{ number_format($discount_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 dark:text-slate-400">Tax</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($tax_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-t border-slate-200 pt-2 text-lg dark:border-slate-700">
                    <dt class="font-semibold text-slate-700 dark:text-slate-200">Grand Total</dt>
                    <dd class="font-bold text-slate-900 dark:text-white">{{ number_format($grand_total, 2) }} {{ $currency }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="grid gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-2 dark:border-slate-800 dark:bg-slate-900">
        <label class="space-y-1 md:col-span-2">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Notes / Terms</span>
            <textarea wire:model="notes" rows="5" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
        </label>
    </div>

    <div class="flex flex-wrap justify-end gap-2">
        <button type="button" wire:click="saveDraft" class="rounded-md border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Save Draft</button>
        <button type="button" wire:click="markSent" class="rounded-md border border-blue-300 px-4 py-2 text-sm text-blue-700 dark:border-blue-500/40 dark:text-blue-300">Mark Sent</button>
        <button type="button" wire:click="previewPdf" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">Preview PDF</button>
    </div>
</div>

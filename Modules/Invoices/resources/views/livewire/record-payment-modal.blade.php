<div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <h2 class="mb-3 text-sm font-semibold text-slate-800 dark:text-slate-100">Record Payment</h2>

    <div class="mb-4 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
        Balance due: <span class="font-semibold">{{ number_format($balanceDue, 2) }} {{ $currency }}</span>
    </div>

    <div class="grid gap-3 md:grid-cols-2">
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Amount</span>
            <input wire:model="amount" type="number" min="0.01" step="0.01" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('amount') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Paid At</span>
            <input wire:model="paid_at" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('paid_at') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Method</span>
            <select wire:model="method" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cash">Cash</option>
                <option value="Cheque">Cheque</option>
                <option value="Internal Credit">Internal Credit</option>
            </select>
            @error('method') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Reference</span>
            <input wire:model="reference" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            @error('reference') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>

        <label class="space-y-1 md:col-span-2">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Notes</span>
            <textarea wire:model="notes" rows="2" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
            @error('notes') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
        </label>
    </div>

    <div class="mt-4 flex justify-end">
        <button type="button" wire:click="save" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">
            Save Payment
        </button>
    </div>
</div>

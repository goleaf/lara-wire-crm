<div class="space-y-4">
    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2">Product</th>
                    <th class="px-3 py-2">Qty</th>
                    <th class="px-3 py-2">Unit Price</th>
                    <th class="px-3 py-2">Discount %</th>
                    <th class="px-3 py-2">Tax %</th>
                    <th class="px-3 py-2">Line Total</th>
                    @if ($editable)
                        <th class="px-3 py-2 text-right">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($lineItems as $index => $item)
                    <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                        <td class="px-3 py-2">
                            @if ($editable)
                                <select wire:model.live="lineItems.{{ $index }}.product_id" wire:change="useProduct({{ $index }}, $event.target.value)" class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900">
                                    <option value="">Manual line</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                                <input wire:model.live="lineItems.{{ $index }}.name" type="text" placeholder="Line item name" class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900" />
                            @else
                                <span class="font-medium text-slate-900 dark:text-slate-100">{{ $item['name'] }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <input wire:model.live="lineItems.{{ $index }}.quantity" type="number" min="0" step="1" @disabled(! $editable) class="w-20 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900" />
                        </td>
                        <td class="px-3 py-2">
                            <input wire:model.live="lineItems.{{ $index }}.unit_price" type="number" min="0" step="0.01" @disabled(! $editable) class="w-28 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900" />
                        </td>
                        <td class="px-3 py-2">
                            <input wire:model.live="lineItems.{{ $index }}.discount" type="number" min="0" max="100" step="0.01" @disabled(! $editable) class="w-24 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900" />
                        </td>
                        <td class="px-3 py-2">
                            <input wire:model.live="lineItems.{{ $index }}.tax_rate" type="number" min="0" max="100" step="0.01" @disabled(! $editable) class="w-24 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900" />
                        </td>
                        <td class="px-3 py-2 font-semibold text-slate-900 dark:text-slate-100">
                            {{ number_format((float) ($item['total'] ?? 0), 2) }}
                        </td>
                        @if ($editable)
                            <td class="px-3 py-2 text-right">
                                <button
                                    type="button"
                                    wire:click="removeRow({{ $index }})"
                                    class="rounded-md border border-rose-300 px-2 py-1 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
                                >
                                    Remove
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $editable ? 7 : 6 }}" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No line items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($editable)
        <button type="button" wire:click="addRow" class="crm-btn crm-btn-secondary">
            Add Row
        </button>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-700 dark:bg-slate-900/60">
        <dl class="grid gap-2 sm:grid-cols-2">
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Subtotal</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($this->subtotal, 2) }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Discount</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($this->discountTotal, 2) }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Tax</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($this->taxTotal, 2) }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Grand Total</dt>
                <dd class="font-semibold text-slate-900 dark:text-white">{{ number_format($this->grandTotal, 2) }}</dd>
            </div>
        </dl>
    </div>
</div>

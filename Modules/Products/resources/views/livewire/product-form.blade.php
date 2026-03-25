<section class="mx-auto max-w-4xl space-y-6">
    <x-crm.status />

    <article class="crm-card p-6">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">
            {{ $productId ? 'Edit Product' : 'Create Product' }}
        </h3>

        <form wire:submit="save" class="mt-6 space-y-5">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
                    <input wire:model.live="name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">SKU</label>
                    <input wire:model.live="sku" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 font-mono text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('sku') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Unit Price</label>
                    <input wire:model.live="unit_price" type="number" step="0.01" min="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('unit_price') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Cost Price</label>
                    <input wire:model.live="cost_price" type="number" step="0.01" min="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('cost_price') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Currency</label>
                    <input wire:model.live="currency" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm uppercase dark:border-slate-700 dark:bg-slate-900" />
                    @error('currency') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Tax Rate (%)</label>
                    <input wire:model.live="tax_rate" type="number" step="0.01" min="0" max="100" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('tax_rate') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Category</label>
                    <select wire:model.live="category_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Uncategorized</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->full_path }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Unit</label>
                    <input wire:model.live="unit" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('unit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                <textarea wire:model.live="description" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 dark:border-slate-700 dark:text-slate-300">
                    <input wire:model.live="active" type="checkbox" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                    Active product
                </label>

                <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 dark:border-slate-700 dark:text-slate-300">
                    <input wire:model.live="recurring" type="checkbox" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                    Recurring subscription
                </label>
            </div>

            @if ($recurring)
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Billing Frequency</label>
                    <select wire:model.live="billing_frequency" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="Monthly">Monthly</option>
                        <option value="Annual">Annual</option>
                    </select>
                    @error('billing_frequency') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-700 dark:bg-slate-900/60">
                <h4 class="font-semibold text-slate-800 dark:text-slate-100">Live Margin Preview</h4>
                <div class="mt-3 grid gap-2 sm:grid-cols-3">
                    <p class="text-slate-600 dark:text-slate-300">Margin: <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($margin, 2) }}</span></p>
                    <p class="text-slate-600 dark:text-slate-300">Margin %: <span class="font-semibold">{{ number_format($marginPercent, 2) }}%</span></p>
                    <p class="text-slate-600 dark:text-slate-300">Price with Tax: <span class="font-semibold text-slate-900 dark:text-white">{{ number_format((float) $unit_price * (1 + ((float) $tax_rate / 100)), 2) }}</span></p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('products.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                    Cancel
                </a>
                <button type="submit" class="crm-btn crm-btn-primary">
                    Save Product
                </button>
            </div>
        </form>
    </article>
</section>

<section class="space-y-6">
    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Product</p>
                <h3 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $product->name }}</h3>
                <p class="mt-2 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $product->sku }}</p>
            </div>

            <a
                href="{{ route('products.edit', $product->id) }}"
                wire:navigate
                class="crm-btn crm-btn-primary"
            >
                Edit
            </a>
        </div>
    </x-crm.card>

    <x-crm.card class="p-6">
        <dl class="grid gap-5 md:grid-cols-2">
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Category</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $product->category?->full_path ?? 'Uncategorized' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Unit</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $product->unit ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Unit Price</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ number_format((float) $product->unit_price, 2) }} {{ $product->currency }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Cost Price</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ number_format((float) $product->cost_price, 2) }} {{ $product->currency }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Margin</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">
                    {{ number_format($product->margin, 2) }} ({{ number_format($product->margin_percent, 2) }}%)
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Price with Tax</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ number_format($product->price_with_tax, 2) }} {{ $product->currency }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Tax Rate</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ number_format((float) $product->tax_rate, 2) }}%</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Billing</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">
                    {{ $product->recurring ? 'Subscription · '.$product->billing_frequency : 'One-time' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</dt>
                <dd class="mt-1">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300' }}">
                        {{ $product->active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Updated</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $product->updated_at->diffForHumans() }}</dd>
            </div>
        </dl>
    </x-crm.card>

    @if ($product->description)
        <x-crm.card class="p-6">
            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Description</h4>
            <p class="mt-3 text-sm leading-relaxed text-slate-700 dark:text-slate-300">{{ $product->description }}</p>
        </x-crm.card>
    @endif
</section>

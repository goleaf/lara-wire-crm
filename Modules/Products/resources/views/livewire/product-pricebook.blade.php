<section class="space-y-6 print:space-y-4">
    <x-crm.card class="p-6 print:rounded-none print:border-none print:p-0 print:shadow-none">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Product Pricebook</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Current active catalog grouped by category.</p>
            </div>
            <a
                href="{{ route('products.index') }}"
                wire:navigate
                class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 print:hidden dark:border-slate-700 dark:text-slate-300"
            >
                Back to Products
            </a>
        </div>
    </x-crm.card>

    @forelse ($groupedProducts as $category => $items)
        <x-crm.card class="overflow-hidden print:rounded-none print:border print:shadow-none">
            <header class="border-b border-slate-200 bg-slate-100/70 px-5 py-3 dark:border-slate-800 dark:bg-slate-900/70">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">{{ $category }}</h4>
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Unit</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Tax</th>
                            <th class="px-4 py-3">Billing</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($items as $product)
                            <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $product->name }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $product->sku }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $product->unit ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ number_format((float) $product->unit_price, 2) }} {{ $product->currency }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ number_format((float) $product->tax_rate, 2) }}%</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $product->recurring ? 'Subscription · '.$product->billing_frequency : 'One-time' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-crm.card>
    @empty
        <article class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-400">
            No active products available.
        </article>
    @endforelse
</section>

<section class="space-y-6">
    <x-crm.status />

    <div class="grid gap-6 lg:grid-cols-[17rem_minmax(0,1fr)]">
        <x-crm.card class="p-5">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Category Filter</h3>
            <div class="mt-4 space-y-2">
                <button
                    wire:click="$set('categoryFilter', '')"
                    class="w-full rounded-xl border px-3 py-2 text-left text-sm {{ $categoryFilter === '' ? 'border-sky-500 bg-sky-50 text-sky-700 dark:bg-sky-500/20 dark:text-sky-200' : 'border-slate-200 text-slate-600 dark:border-slate-700 dark:text-slate-300' }}"
                >
                    All categories
                </button>
                @foreach ($categories as $category)
                    <button
                        wire:click="$set('categoryFilter', '{{ $category->id }}')"
                        class="w-full rounded-xl border px-3 py-2 text-left text-sm {{ $categoryFilter === $category->id ? 'border-sky-500 bg-sky-50 text-sky-700 dark:bg-sky-500/20 dark:text-sky-200' : 'border-slate-200 text-slate-600 dark:border-slate-700 dark:text-slate-300' }}"
                    >
                        {{ $category->full_path }}
                    </button>
                @endforeach
            </div>
        </x-crm.card>

        <div class="space-y-6">
            <x-crm.card class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Products</h3>
                    <div class="flex items-center gap-2">
                        <a
                            href="{{ route('products.categories') }}"
                            wire:navigate
                            class="crm-btn crm-btn-secondary"
                        >
                            Categories
                        </a>
                        <a
                            href="{{ route('products.create') }}"
                            wire:navigate
                            class="crm-btn crm-btn-primary"
                        >
                            New Product
                        </a>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 md:grid-cols-5">
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Search name or SKU"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
                    />

                    <select wire:model.live="activeFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Any status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>

                    <select wire:model.live="recurringFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Any plan</option>
                        <option value="1">Recurring</option>
                        <option value="0">One-time</option>
                    </select>

                    <select wire:model.live="billingFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">Any billing</option>
                        <option value="One-time">One-time</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Annual">Annual</option>
                    </select>

                    <a
                        href="{{ route('products.pricebook') }}"
                        wire:navigate
                        class="inline-flex items-center justify-center crm-btn crm-btn-secondary"
                    >
                        Pricebook
                    </a>
                </div>
            </x-crm.card>

            <x-crm.card class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Unit Price</th>
                                <th class="px-4 py-3">Cost</th>
                                <th class="px-4 py-3">Margin %</th>
                                <th class="px-4 py-3">Tax</th>
                                <th class="px-4 py-3">Billing</th>
                                <th class="px-4 py-3">Active</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($products as $product)
                                @php
                                    $marginClass = $product->margin_percent < 10
                                        ? 'text-rose-600 dark:text-rose-300'
                                        : ($product->margin_percent <= 30
                                            ? 'text-amber-600 dark:text-amber-300'
                                            : 'text-emerald-600 dark:text-emerald-300');
                                @endphp
                                <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('products.show', $product->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                            {{ $product->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $product->sku }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $product->category?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ number_format((float) $product->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ number_format((float) $product->cost_price, 2) }}</td>
                                    <td class="px-4 py-3 font-semibold {{ $marginClass }}">{{ number_format($product->margin_percent, 1) }}%</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ number_format((float) $product->tax_rate, 2) }}%</td>
                                    <td class="px-4 py-3">
                                        @if ($product->recurring)
                                            <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                                Subscription {{ $product->billing_frequency }}
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-500/20 dark:text-slate-300">
                                                One-time
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            wire:click="toggleActive('{{ $product->id }}')"
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300' }}"
                                        >
                                            {{ $product->active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('products.edit', $product->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                                Edit
                                            </a>
                                            <button
                                                wire:click="delete('{{ $product->id }}')"
                                                onclick="return confirm('Delete this product?')"
                                                class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                                        No products found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
                    {{ $products->links() }}
                </div>
            </x-crm.card>
        </div>
    </div>
</section>

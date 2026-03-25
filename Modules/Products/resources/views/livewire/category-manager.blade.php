<section class="space-y-6">
    <x-crm.status />

    <article class="crm-card p-6">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Category Manager</h3>

        <form wire:submit="createCategory" class="mt-5 grid gap-3 md:grid-cols-4">
            <input wire:model.live="name" type="text" placeholder="Category name" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <input wire:model.live="description" type="text" placeholder="Description" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <select wire:model.live="parent_id" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">No parent</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->full_path }}</option>
                @endforeach
            </select>
            <button type="submit" class="crm-btn crm-btn-primary">
                Add Category
            </button>
        </form>
        @error('name') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror
    </article>

    <article class="crm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Path</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Products</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($categories as $category)
                        <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">
                                @if ($editingCategoryId === $category->id)
                                    <input wire:model.live="editingName" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900" />
                                @else
                                    {{ $category->full_path }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                @if ($editingCategoryId === $category->id)
                                    <input wire:model.live="editingDescription" type="text" class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-900" />
                                @else
                                    {{ $category->description ?: '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $category->products_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    @if ($editingCategoryId === $category->id)
                                        <button wire:click="saveEdit" class="rounded-lg border border-emerald-300 px-3 py-1.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/40 dark:text-emerald-300">
                                            Save
                                        </button>
                                        <button wire:click="cancelEdit" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                                            Cancel
                                        </button>
                                    @else
                                        <button wire:click="beginEdit('{{ $category->id }}')" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                                            Edit
                                        </button>
                                        <button
                                            wire:click="deleteCategory('{{ $category->id }}')"
                                            onclick="return confirm('Delete this category?')"
                                            class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>

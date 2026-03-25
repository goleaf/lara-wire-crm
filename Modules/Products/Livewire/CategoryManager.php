<?php

namespace Modules\Products\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Products\Models\ProductCategory;

class CategoryManager extends Component
{
    public string $name = '';

    public string $description = '';

    public string $parent_id = '';

    public ?string $editingCategoryId = null;

    public string $editingName = '';

    public string $editingDescription = '';

    public string $editingParentId = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('products.view'), 403);
    }

    public function createCategory(): void
    {
        abort_unless(auth()->user()?->can('products.create'), 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', Rule::exists('product_categories', 'id')],
        ]);

        ProductCategory::query()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] !== '' ? $validated['description'] : null,
            'parent_id' => $validated['parent_id'] !== '' ? $validated['parent_id'] : null,
        ]);

        $this->reset(['name', 'description', 'parent_id']);
        session()->flash('status', 'Category created.');
    }

    public function beginEdit(string $id): void
    {
        abort_unless(auth()->user()?->can('products.edit'), 403);

        $category = ProductCategory::query()
            ->select(['id', 'name', 'description', 'parent_id'])
            ->findOrFail($id);

        $this->editingCategoryId = $category->id;
        $this->editingName = (string) $category->name;
        $this->editingDescription = (string) ($category->description ?? '');
        $this->editingParentId = (string) ($category->parent_id ?? '');
    }

    public function saveEdit(): void
    {
        abort_unless(auth()->user()?->can('products.edit'), 403);

        if (! $this->editingCategoryId) {
            return;
        }

        $validated = $this->validate([
            'editingName' => ['required', 'string', 'max:255'],
            'editingDescription' => ['nullable', 'string'],
            'editingParentId' => ['nullable', Rule::exists('product_categories', 'id')],
        ]);

        ProductCategory::query()->whereKey($this->editingCategoryId)->update([
            'name' => $validated['editingName'],
            'description' => $validated['editingDescription'] !== '' ? $validated['editingDescription'] : null,
            'parent_id' => $validated['editingParentId'] !== '' ? $validated['editingParentId'] : null,
        ]);

        $this->reset(['editingCategoryId', 'editingName', 'editingDescription', 'editingParentId']);
        session()->flash('status', 'Category updated.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingCategoryId', 'editingName', 'editingDescription', 'editingParentId']);
    }

    public function deleteCategory(string $id): void
    {
        abort_unless(auth()->user()?->can('products.delete'), 403);

        $category = ProductCategory::query()
            ->withCount(['children', 'products'])
            ->findOrFail($id);

        if ($category->children_count > 0) {
            session()->flash('status', 'Cannot delete category with child categories.');

            return;
        }

        if ($category->products_count > 0) {
            session()->flash('status', 'Cannot delete category with products.');

            return;
        }

        $category->delete();
        session()->flash('status', 'Category deleted.');
    }

    public function render(): View
    {
        $categories = ProductCategory::query()
            ->select(['id', 'name', 'description', 'parent_id'])
            ->with([
                'parent:id,name,parent_id',
            ])
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('products::livewire.category-manager', [
            'categories' => $categories,
        ])->extends('core::layouts.module', ['title' => 'Category Manager']);
    }
}

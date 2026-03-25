<?php

namespace Modules\Files\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Files\Models\CrmFile;

class FilePicker extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $multiple = true;

    /**
     * @var array<int, string>
     */
    public array $selectedIds = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleSelection(string $id): void
    {
        if (in_array($id, $this->selectedIds, true)) {
            $this->selectedIds = array_values(array_filter(
                $this->selectedIds,
                fn (string $selected): bool => $selected !== $id
            ));

            return;
        }

        if (! $this->multiple) {
            $this->selectedIds = [$id];

            return;
        }

        $this->selectedIds[] = $id;
    }

    public function confirmSelection(): void
    {
        $this->dispatch('filesSelected', fileIds: $this->selectedIds);
    }

    public function render(): View
    {
        return view('files::livewire.file-picker', [
            'files' => CrmFile::query()
                ->select(['id', 'name', 'extension', 'size_bytes', 'created_at'])
                ->when($this->search !== '', function ($query): void {
                    $query->where(function ($inner): void {
                        $inner
                            ->where('name', 'like', "%{$this->search}%")
                            ->orWhere('original_filename', 'like', "%{$this->search}%");
                    });
                })
                ->latest()
                ->paginate(12),
        ]);
    }
}

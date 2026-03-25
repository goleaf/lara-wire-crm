<?php

namespace Modules\Files\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Files\Models\CrmFile;
use Modules\Files\Services\FileService;

class FileManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $uploadedByFilter = '';

    public string $viewMode = 'grid';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public ?string $selectedFileId = null;

    public ?string $renamingFileId = null;

    public string $newName = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasPermission('view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function setViewMode(string $mode): void
    {
        if (! in_array($mode, ['grid', 'list'], true)) {
            return;
        }

        $this->viewMode = $mode;
    }

    public function pickFile(string $id): void
    {
        $this->selectedFileId = $id;
    }

    public function startRename(string $id): void
    {
        $file = CrmFile::query()->select(['id', 'name'])->findOrFail($id);
        $this->renamingFileId = $file->id;
        $this->newName = $file->name;
    }

    public function saveRename(): void
    {
        abort_unless(auth()->user()?->hasPermission('edit'), 403);

        $validated = $this->validate([
            'newName' => ['required', 'string', 'max:255'],
        ]);

        CrmFile::query()->whereKey($this->renamingFileId)->update([
            'name' => $validated['newName'],
        ]);

        $this->renamingFileId = null;
        $this->newName = '';
        session()->flash('status', 'File renamed.');
    }

    public function deleteFile(string $id): void
    {
        abort_unless(auth()->user()?->hasPermission('delete'), 403);

        $file = CrmFile::query()->findOrFail($id);
        app(FileService::class)->delete($file);

        if ($this->selectedFileId === $id) {
            $this->selectedFileId = null;
        }

        session()->flash('status', 'File deleted.');
        $this->resetPage();
    }

    public function render(): View
    {
        $files = CrmFile::query()
            ->select([
                'id',
                'name',
                'original_filename',
                'mime_type',
                'extension',
                'size_bytes',
                'uploaded_by',
                'created_at',
                'version',
            ])
            ->with(['uploadedBy:id,full_name'])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($inner): void {
                    $inner
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('original_filename', 'like', "%{$this->search}%")
                        ->orWhere('extension', 'like', "%{$this->search}%");
                });
            })
            ->when($this->uploadedByFilter !== '', fn ($query) => $query->where('uploaded_by', $this->uploadedByFilter))
            ->when($this->typeFilter === 'image', fn ($query) => $query->where('mime_type', 'like', 'image/%'))
            ->when($this->typeFilter === 'pdf', fn ($query) => $query->where('mime_type', 'application/pdf'))
            ->when($this->typeFilter === 'doc', fn ($query) => $query->whereIn('extension', ['doc', 'docx', 'xls', 'xlsx', 'csv', 'txt']))
            ->when($this->typeFilter === 'other', function ($query): void {
                $query
                    ->where('mime_type', 'not like', 'image/%')
                    ->where('mime_type', '!=', 'application/pdf')
                    ->whereNotIn('extension', ['doc', 'docx', 'xls', 'xlsx', 'csv', 'txt']);
            })
            ->when($this->dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate($this->viewMode === 'grid' ? 24 : 15);

        $uploaders = CrmFile::query()
            ->select(['uploaded_by'])
            ->with('uploadedBy:id,full_name')
            ->distinct()
            ->get()
            ->pluck('uploadedBy')
            ->filter()
            ->unique('id')
            ->values();

        return view('files::livewire.file-manager', [
            'files' => $files,
            'uploaders' => $uploaders,
        ])->extends('core::layouts.module', ['title' => 'Files']);
    }
}

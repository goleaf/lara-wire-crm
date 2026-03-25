<?php

namespace Modules\Files\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Files\Models\CrmFile;
use Modules\Files\Services\FileService;

class FileList extends Component
{
    use WithPagination;

    public string $relatedType;

    public string $relatedId;

    public function mount(string $relatedType, string $relatedId): void
    {
        $this->relatedType = $relatedType;
        $this->relatedId = $relatedId;
    }

    public function deleteFile(string $id): void
    {
        abort_unless(auth()->user()?->hasPermission('delete'), 403);

        $file = CrmFile::query()->findOrFail($id);
        app(FileService::class)->delete($file);
        $this->dispatch('files-updated');
    }

    public function render(): View
    {
        return view('files::livewire.file-list', [
            'files' => CrmFile::query()
                ->select([
                    'id',
                    'name',
                    'original_filename',
                    'mime_type',
                    'extension',
                    'size_bytes',
                    'uploaded_by',
                    'version',
                    'created_at',
                ])
                ->forRecord($this->relatedType, $this->relatedId)
                ->with('uploadedBy:id,full_name')
                ->latest()
                ->paginate(10),
        ]);
    }
}

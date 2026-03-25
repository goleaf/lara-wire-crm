<?php

namespace Modules\Files\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Files\Models\CrmFile;

#[Defer]
class FilePreview extends Component
{
    public string $fileId;

    public function mount(string $fileId): void
    {
        $this->fileId = $fileId;
    }

    public function previous(): void
    {
        $file = CrmFile::query()->findOrFail($this->fileId);

        $previous = CrmFile::query()
            ->select(['id', 'created_at'])
            ->where('created_at', '<', $file->created_at)
            ->orderByDesc('created_at')
            ->first();

        if ($previous) {
            $this->fileId = $previous->id;
        }
    }

    public function next(): void
    {
        $file = CrmFile::query()->findOrFail($this->fileId);

        $next = CrmFile::query()
            ->select(['id', 'created_at'])
            ->where('created_at', '>', $file->created_at)
            ->orderBy('created_at')
            ->first();

        if ($next) {
            $this->fileId = $next->id;
        }
    }

    public function render(): View
    {
        return view('files::livewire.file-preview', [
            'file' => CrmFile::query()
                ->with(['uploadedBy:id,full_name', 'versions'])
                ->findOrFail($this->fileId),
        ]);
    }
}

<?php

namespace Modules\Files\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Files\Models\CrmFile;

#[Defer]
class FileUploadZone extends Component
{
    use WithFileUploads;

    public ?string $relatedType = null;

    public ?string $relatedId = null;

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $uploads = [];

    public function mount(?string $relatedType = null, ?string $relatedId = null): void
    {
        $this->relatedType = $relatedType;
        $this->relatedId = $relatedId;
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->hasPermission('create'), 403);

        $this->validate([
            'uploads' => ['required', 'array', 'min:1'],
            'uploads.*' => ['file', 'max:'.((int) config('files.max_size_kb', 10240))],
        ]);

        foreach ($this->uploads as $upload) {
            CrmFile::upload($upload, [
                'uploaded_by' => auth()->user(),
                'related_to_type' => $this->relatedType,
                'related_to_id' => $this->relatedId,
            ]);
        }

        $this->uploads = [];
        $this->dispatch('files-updated');
        session()->flash('status', 'Files uploaded.');
    }

    public function render(): View
    {
        return view('files::livewire.file-upload-zone');
    }
}

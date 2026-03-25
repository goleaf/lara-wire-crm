<?php

namespace Modules\Core\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Attributes\On;
use Livewire\Component;

#[Defer]
class ConfirmModal extends Component
{
    public bool $open = false;

    public string $message = 'Are you sure?';

    public string $actionEvent = '';

    /**
     * @var array<string, mixed>
     */
    public array $actionPayload = [];

    #[On('confirm')]
    public function show(array $payload): void
    {
        $this->message = trim((string) ($payload['message'] ?? 'Are you sure?'));
        $this->actionEvent = (string) ($payload['action'] ?? '');
        $this->actionPayload = (array) ($payload['payload'] ?? []);
        $this->open = true;
    }

    public function cancel(): void
    {
        $this->resetModal();
    }

    public function confirm(): void
    {
        if ($this->actionEvent !== '') {
            $this->dispatch($this->actionEvent, payload: $this->actionPayload);
        }

        $this->resetModal();
    }

    public function render(): View
    {
        return view('core::livewire.confirm-modal');
    }

    protected function resetModal(): void
    {
        $this->open = false;
        $this->message = 'Are you sure?';
        $this->actionEvent = '';
        $this->actionPayload = [];
    }
}

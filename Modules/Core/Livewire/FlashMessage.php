<?php

namespace Modules\Core\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Attributes\On;
use Livewire\Component;

#[Defer]
class FlashMessage extends Component
{
    /**
     * @var array<int, array{id: string, type: string, message: string}>
     */
    public array $toasts = [];

    #[On('flash')]
    public function addToast(array $payload): void
    {
        $message = trim((string) ($payload['message'] ?? ''));

        if ($message === '') {
            return;
        }

        $this->toasts[] = [
            'id' => (string) str()->uuid(),
            'type' => (string) ($payload['type'] ?? 'info'),
            'message' => $message,
        ];
    }

    public function dismiss(string $id): void
    {
        $this->toasts = array_values(array_filter(
            $this->toasts,
            fn (array $toast): bool => $toast['id'] !== $id
        ));
    }

    public function render(): View
    {
        return view('core::livewire.flash-message');
    }
}

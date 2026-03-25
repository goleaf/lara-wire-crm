<?php

namespace Modules\Messaging\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Defer;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Messaging\Services\MessagingService;

#[Defer]
class DirectMessageModal extends Component
{
    public bool $isOpen = false;

    public string $search = '';

    #[On('open-direct-message')]
    public function open(): void
    {
        abort_unless(auth()->user()?->can('messaging.create'), 403);

        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->search = '';
    }

    public function startDm(string $userId, MessagingService $messagingService): void
    {
        abort_unless(auth()->user()?->can('messaging.create'), 403);

        $target = User::query()
            ->select(['id', 'full_name', 'email'])
            ->whereKey($userId)
            ->firstOrFail();

        $channel = $messagingService->createDirectChannel(auth()->user(), $target);

        $this->close();
        $this->dispatch('channel-selected', channelId: $channel->getKey());
    }

    public function render(): View
    {
        return view('messaging::livewire.direct-message-modal', [
            'users' => $this->searchUsers(),
        ]);
    }

    /**
     * @return Collection<int, User>
     */
    protected function searchUsers(): Collection
    {
        return User::query()
            ->select(['id', 'full_name', 'email'])
            ->whereKeyNot((string) auth()->id())
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $inner): void {
                    $inner
                        ->where('full_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('full_name')
            ->limit(20)
            ->get();
    }
}

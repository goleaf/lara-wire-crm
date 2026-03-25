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
class CreateChannelModal extends Component
{
    public bool $isOpen = false;

    public string $type = 'Public';

    public string $name = '';

    public string $memberSearch = '';

    /**
     * @var array<int, string>
     */
    public array $memberIds = [];

    public string $relatedType = '';

    public string $relatedId = '';

    #[On('open-create-channel')]
    public function open(): void
    {
        abort_unless(auth()->user()?->can('messaging.create'), 403);

        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->reset([
            'isOpen',
            'type',
            'name',
            'memberSearch',
            'memberIds',
            'relatedType',
            'relatedId',
        ]);

        $this->type = 'Public';
    }

    public function toggleMember(string $userId): void
    {
        if (in_array($userId, $this->memberIds, true)) {
            $this->memberIds = array_values(array_filter($this->memberIds, fn (string $id): bool => $id !== $userId));

            return;
        }

        $this->memberIds[] = $userId;
    }

    public function create(MessagingService $messagingService): void
    {
        abort_unless(auth()->user()?->can('messaging.create'), 403);

        $validated = $this->validate([
            'type' => ['required', 'in:Public,Private'],
            'name' => ['required', 'string', 'max:255'],
            'memberIds' => ['array'],
            'memberIds.*' => ['uuid', 'exists:users,id'],
            'relatedType' => ['nullable', 'string', 'max:255'],
            'relatedId' => ['nullable', 'uuid'],
        ]);

        $channel = $messagingService->createGroupChannel(
            $validated['name'],
            $validated['memberIds'],
            auth()->user(),
            $validated['type'],
            $this->nullableString($validated['relatedType']),
            $this->nullableString($validated['relatedId']),
        );

        $this->close();
        $this->dispatch('channel-created', channelId: $channel->getKey());
        $this->dispatch('channel-selected', channelId: $channel->getKey());
    }

    public function render(): View
    {
        return view('messaging::livewire.create-channel-modal', [
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
            ->when($this->memberSearch !== '', function (Builder $query): void {
                $query->where(function (Builder $inner): void {
                    $inner
                        ->where('full_name', 'like', '%'.$this->memberSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->memberSearch.'%');
                });
            })
            ->orderBy('full_name')
            ->limit(20)
            ->get();
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}

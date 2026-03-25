<?php

namespace Modules\Messaging\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Messaging\Models\Channel;

#[Defer]
class ChannelSidebar extends Component
{
    public ?string $selectedChannelId = null;

    public function mount(?string $selectedChannelId = null): void
    {
        $this->selectedChannelId = $selectedChannelId;
    }

    #[On('channel-selected')]
    public function syncSelection(string $channelId): void
    {
        $this->selectedChannelId = $channelId;
    }

    public function selectChannel(string $channelId): void
    {
        $this->selectedChannelId = $channelId;
        $this->dispatch('channel-selected', channelId: $channelId);
    }

    public function render(): View
    {
        $user = auth()->user();

        $channels = Channel::query()
            ->select(['id', 'name', 'type', 'related_to_type', 'related_to_id', 'created_by', 'updated_at'])
            ->forUser((string) auth()->id())
            ->with('members:id,full_name,email')
            ->orderByDesc('updated_at')
            ->get();

        $unread = $channels->mapWithKeys(fn (Channel $channel) => [
            (string) $channel->getKey() => $channel->getUnreadCount($user),
        ]);

        return view('messaging::livewire.channel-sidebar', [
            'privateChannels' => $channels->where('type', 'Private')->values(),
            'publicChannels' => $channels->where('type', 'Public')->values(),
            'directChannels' => $channels->where('type', 'Direct')->values(),
            'unread' => $unread,
        ]);
    }
}

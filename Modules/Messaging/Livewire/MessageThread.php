<?php

namespace Modules\Messaging\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Messaging\Models\Channel;
use Modules\Messaging\Models\Message;

#[Defer]
class MessageThread extends Component
{
    public ?string $channelId = null;

    public int $pageSize = 25;

    public int $page = 1;

    public function mount(?string $channelId = null): void
    {
        $this->channelId = $channelId;
    }

    #[On('channel-selected')]
    public function changeChannel(string $channelId): void
    {
        $this->channelId = $channelId;
        $this->page = 1;
    }

    #[On('message-sent')]
    public function refreshThread(): void {}

    public function loadMore(): void
    {
        $this->page++;
    }

    public function deleteMessage(string $messageId): void
    {
        abort_unless(auth()->user()?->can('messaging.delete'), 403);

        Message::query()
            ->where('channel_id', $this->channelId)
            ->where('sender_id', auth()->id())
            ->whereKey($messageId)
            ->update([
                'is_deleted' => true,
                'edited_at' => now(),
                'body' => '',
            ]);
    }

    public function replyTo(string $messageId): void
    {
        $this->dispatch('reply-selected', messageId: $messageId);
    }

    public function render(): View
    {
        if (! $this->channelId) {
            return view('messaging::livewire.message-thread', [
                'channel' => null,
                'messages' => collect(),
                'hasMore' => false,
            ]);
        }

        $channel = Channel::query()
            ->select(['id', 'name', 'type', 'created_by'])
            ->forUser((string) auth()->id())
            ->whereKey($this->channelId)
            ->with('members:id,full_name,email')
            ->first();

        if (! $channel) {
            return view('messaging::livewire.message-thread', [
                'channel' => null,
                'messages' => collect(),
                'hasMore' => false,
            ]);
        }

        $limit = $this->pageSize * $this->page;

        $baseQuery = Message::query()
            ->select([
                'id',
                'channel_id',
                'sender_id',
                'body',
                'sent_at',
                'edited_at',
                'is_deleted',
                'parent_message_id',
            ])
            ->where('channel_id', $this->channelId)
            ->whereNull('parent_message_id')
            ->with([
                'sender:id,full_name,avatar_path',
                'attachments:id,name,storage_path,disk,mime_type,extension',
                'replies' => function ($query): void {
                    $query->select([
                        'id',
                        'channel_id',
                        'sender_id',
                        'body',
                        'sent_at',
                        'edited_at',
                        'is_deleted',
                        'parent_message_id',
                    ])->with('sender:id,full_name,avatar_path');
                },
            ]);

        $total = (clone $baseQuery)->count();

        $messages = $baseQuery
            ->orderByDesc('sent_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        $channel->markAsRead(auth()->user());

        return view('messaging::livewire.message-thread', [
            'channel' => $channel,
            'messages' => $messages,
            'hasMore' => $total > $messages->count(),
        ]);
    }
}

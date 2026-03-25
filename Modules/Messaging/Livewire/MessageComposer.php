<?php

namespace Modules\Messaging\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Files\Models\CrmFile;
use Modules\Messaging\Models\Channel;
use Modules\Messaging\Models\Message;
use Modules\Messaging\Services\MessagingService;

class MessageComposer extends Component
{
    use WithFileUploads;

    public ?string $channelId = null;

    public string $body = '';

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $uploads = [];

    public ?string $replyToId = null;

    public string $mentionSearch = '';

    public function mount(?string $channelId = null): void
    {
        $this->channelId = $channelId;
    }

    #[On('channel-selected')]
    public function onChannelSelected(string $channelId): void
    {
        $this->channelId = $channelId;
        $this->body = '';
        $this->uploads = [];
        $this->replyToId = null;
        $this->mentionSearch = '';
    }

    #[On('reply-selected')]
    public function onReplySelected(string $messageId): void
    {
        $this->replyToId = $messageId;
    }

    #[On('messaging-layout-send')]
    public function onParentSend(MessagingService $messagingService): void
    {
        $this->send($messagingService);
    }

    public function updatedBody(string $value): void
    {
        if (preg_match('/@([A-Za-z0-9._-]{1,40})$/', $value, $matches) === 1) {
            $this->mentionSearch = (string) ($matches[1] ?? '');

            return;
        }

        $this->mentionSearch = '';
    }

    public function insertMention(string $value): void
    {
        $this->body = preg_replace('/@([A-Za-z0-9._-]{0,40})$/', '@'.$value.' ', $this->body) ?? $this->body.'@'.$value.' ';
        $this->mentionSearch = '';
    }

    public function addEmoji(string $emoji): void
    {
        $this->body = rtrim($this->body.' '.$emoji).' ';
    }

    public function clearReply(): void
    {
        $this->replyToId = null;
    }

    public function send(MessagingService $messagingService): void
    {
        abort_unless(auth()->user()?->can('messaging.create'), 403);

        if (! $this->channelId) {
            return;
        }

        $validated = $this->validate([
            'body' => ['nullable', 'string'],
            'uploads.*' => ['file', 'max:10240'],
        ]);

        $channel = Channel::query()
            ->select(['id', 'name', 'type', 'created_by'])
            ->forUser((string) auth()->id())
            ->whereKey($this->channelId)
            ->first();

        if (! $channel) {
            return;
        }

        $fileIds = [];

        if ($this->uploads !== []) {
            $fileIds = collect($this->uploads)
                ->map(function (TemporaryUploadedFile $upload): ?string {
                    if (! class_exists(CrmFile::class)) {
                        return null;
                    }

                    $file = CrmFile::upload($upload, [
                        'uploaded_by' => auth()->user(),
                        'related_to_type' => Channel::class,
                        'related_to_id' => $this->channelId,
                    ]);

                    return (string) $file->getKey();
                })
                ->filter()
                ->values()
                ->all();
        }

        $payload = trim((string) ($validated['body'] ?? ''));

        if ($payload === '' && $fileIds === []) {
            return;
        }

        $message = $messagingService->sendMessage(
            $channel,
            auth()->user(),
            $payload,
            $fileIds,
            $this->replyToId
        );

        $this->body = '';
        $this->uploads = [];
        $this->replyToId = null;
        $this->mentionSearch = '';

        $this->dispatch('message-sent', messageId: $message->getKey());
        $this->dispatch('notifications-refresh');
    }

    public function render(): View
    {
        $replyMessage = $this->replyToId
            ? Message::query()
                ->select(['id', 'body', 'sender_id'])
                ->with('sender:id,full_name')
                ->whereKey($this->replyToId)
                ->first()
            : null;

        return view('messaging::livewire.message-composer', [
            'mentionUsers' => $this->mentionCandidates(),
            'replyMessage' => $replyMessage,
        ]);
    }

    /**
     * @return Collection<int, User>
     */
    protected function mentionCandidates(): Collection
    {
        if ($this->mentionSearch === '') {
            return collect();
        }

        return User::query()
            ->select(['id', 'full_name', 'email'])
            ->where(function (Builder $query): void {
                $query
                    ->where('full_name', 'like', '%'.$this->mentionSearch.'%')
                    ->orWhere('email', 'like', $this->mentionSearch.'%');
            })
            ->orderBy('full_name')
            ->limit(8)
            ->get();
    }
}

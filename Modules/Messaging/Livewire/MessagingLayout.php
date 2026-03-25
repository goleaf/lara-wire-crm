<?php

namespace Modules\Messaging\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Messaging\Models\Channel;

class MessagingLayout extends Component
{
    public ?string $selectedChannelId = null;

    public function mount(?string $channelId = null): void
    {
        abort_unless(auth()->user()?->can('messaging.view'), 403);

        if ($channelId && $this->userCanAccessChannel($channelId)) {
            $this->selectedChannelId = $channelId;

            return;
        }

        $this->selectedChannelId = Channel::query()
            ->select(['channels.id'])
            ->forUser((string) auth()->id())
            ->orderByDesc('updated_at')
            ->value('id');
    }

    public function openCreateChannel(): void
    {
        abort_unless(auth()->user()?->can('messaging.create'), 403);

        $this->dispatch('open-create-channel');
    }

    public function openDirectMessage(): void
    {
        abort_unless(auth()->user()?->can('messaging.create'), 403);

        $this->dispatch('open-direct-message');
    }

    /**
     * Fallback relay for nested composer submits.
     * In rare hydration/routing edge-cases, a form action can resolve to the parent component.
     */
    public function send(): void
    {
        $this->dispatch('messaging-layout-send');
    }

    #[On('channel-selected')]
    public function setChannel(string $channelId): void
    {
        if (! $this->userCanAccessChannel($channelId)) {
            return;
        }

        $this->selectedChannelId = $channelId;
    }

    #[On('channel-created')]
    public function handleChannelCreated(string $channelId): void
    {
        $this->setChannel($channelId);
    }

    public function render(): View
    {
        return view('messaging::livewire.messaging-layout')
            ->extends('core::layouts.module', ['title' => 'Messages']);
    }

    protected function userCanAccessChannel(string $channelId): bool
    {
        return Channel::query()
            ->select(['channels.id'])
            ->forUser((string) auth()->id())
            ->whereKey($channelId)
            ->exists();
    }
}

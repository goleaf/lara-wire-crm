<div class="space-y-4">
    <div class="grid gap-4 lg:grid-cols-[20rem_minmax(0,1fr)]">
        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Channels</h2>
                <div class="flex gap-2">
                    <button
                        type="button"
                        wire:click="openCreateChannel"
                        class="rounded-md border border-slate-300 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                    >
                        New Channel
                    </button>
                    <button
                        type="button"
                        wire:click="openDirectMessage"
                        class="rounded-md bg-sky-600 px-2 py-1 text-xs font-medium text-white hover:bg-sky-500"
                    >
                        New DM
                    </button>
                </div>
            </div>

            @livewire(\Modules\Messaging\Livewire\ChannelSidebar::class, ['selectedChannelId' => $selectedChannelId], key('messaging-sidebar-'.$selectedChannelId))
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            @if ($selectedChannelId)
                <div class="flex h-[calc(100vh-15rem)] flex-col">
                    <div class="min-h-0 flex-1 overflow-hidden">
                        @livewire(\Modules\Messaging\Livewire\MessageThread::class, ['channelId' => $selectedChannelId], key('message-thread-'.$selectedChannelId))
                    </div>
                    <div class="border-t border-slate-200 p-3 dark:border-slate-800">
                        @livewire(\Modules\Messaging\Livewire\MessageComposer::class, ['channelId' => $selectedChannelId], key('message-composer-'.$selectedChannelId))
                    </div>
                </div>
            @else
                <div class="flex h-[calc(100vh-15rem)] items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200">No channel selected</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pick a channel or create a new conversation.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>

    @livewire(\Modules\Messaging\Livewire\CreateChannelModal::class, key('create-channel-modal'))
    @livewire(\Modules\Messaging\Livewire\DirectMessageModal::class, key('direct-message-modal'))
</div>

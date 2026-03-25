<div wire:poll.5s class="space-y-4">
    @php
        $groups = [
            'Public' => $publicChannels,
            'Private' => $privateChannels,
            'Direct Messages' => $directChannels,
        ];
    @endphp

    @foreach ($groups as $label => $items)
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <div class="space-y-1">
                @forelse ($items as $channel)
                    @php
                        $isActive = $selectedChannelId === $channel->id;
                        $unreadCount = (int) ($unread[$channel->id] ?? 0);
                    @endphp

                    <button
                        type="button"
                        wire:click="selectChannel('{{ $channel->id }}')"
                        class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-left text-sm transition {{ $isActive ? 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-200' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}"
                    >
                        <span class="{{ $unreadCount > 0 ? 'font-semibold' : '' }}">
                            {{ $channel->display_name }}
                        </span>
                        <span class="flex items-center gap-1">
                            @if (! empty($channel->related_to_type))
                                <span class="rounded bg-violet-100 px-1.5 py-0.5 text-[10px] font-medium text-violet-700 dark:bg-violet-900/30 dark:text-violet-200">Linked</span>
                            @endif
                            @if ($unreadCount > 0)
                                <span class="min-w-5 rounded-full bg-rose-500 px-1.5 py-0.5 text-center text-[10px] font-semibold text-white">{{ $unreadCount }}</span>
                            @endif
                        </span>
                    </button>
                @empty
                    <p class="rounded-md border border-dashed border-slate-300 px-2 py-2 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        No channels yet.
                    </p>
                @endforelse
            </div>
        </div>
    @endforeach
</div>

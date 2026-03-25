<div wire:poll.3s class="flex h-full flex-col">
    @if (! $channel)
        <div class="flex h-full items-center justify-center text-sm text-slate-500 dark:text-slate-400">
            Select a channel to start chatting.
        </div>
    @else
        <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-800">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $channel->display_name }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $channel->type }} channel</p>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-4 py-3">
            @if ($hasMore)
                <div class="mb-4 text-center">
                    <button
                        type="button"
                        wire:click="loadMore"
                        class="rounded-md border border-slate-300 px-3 py-1 text-xs text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Load older messages
                    </button>
                </div>
            @endif

            <div class="space-y-4">
                @forelse ($messages as $message)
                    @php
                        $isMine = (string) $message->sender_id === (string) auth()->id();
                    @endphp
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] space-y-2">
                            <div class="rounded-lg px-3 py-2 {{ $isMine ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-slate-100' }}">
                                <div class="mb-1 flex items-center gap-2 text-[11px]">
                                    <span class="font-semibold">{{ $isMine ? 'You' : $message->sender?->full_name }}</span>
                                    <span class="{{ $isMine ? 'text-sky-100' : 'text-slate-500 dark:text-slate-400' }}">
                                        {{ $message->sent_at?->format('H:i') }}
                                        @if ($message->edited_at)
                                            (edited)
                                        @endif
                                    </span>
                                </div>

                                @if ($message->is_deleted)
                                    <p class="italic opacity-80">Message deleted</p>
                                @else
                                    @php
                                        $formatted = preg_replace(
                                            '/@([A-Za-z0-9._-]+)/',
                                            '<span class="rounded bg-yellow-200 px-1 text-yellow-900">@$1</span>',
                                            e($message->body)
                                        );
                                    @endphp
                                    <p class="whitespace-pre-wrap break-words text-sm">{!! $formatted !!}</p>
                                @endif

                                @if ($message->attachments->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($message->attachments as $file)
                                            <a
                                                href="{{ route('files.download', $file->id) }}"
                                                class="rounded bg-white/20 px-2 py-0.5 text-xs underline-offset-2 hover:underline"
                                            >
                                                {{ $file->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                                <button type="button" wire:click="replyTo('{{ $message->id }}')" class="hover:text-slate-700 dark:hover:text-slate-200">Reply</button>
                                @if ($isMine && ! $message->is_deleted)
                                    <button type="button" wire:click="deleteMessage('{{ $message->id }}')" class="hover:text-rose-600">Delete</button>
                                @endif
                            </div>

                            @if ($message->replies->isNotEmpty())
                                <div class="ml-4 space-y-2 border-l border-slate-200 pl-3 dark:border-slate-700">
                                    @foreach ($message->replies as $reply)
                                        <div class="rounded-md bg-slate-50 px-2 py-1 text-xs dark:bg-slate-800/80">
                                            <span class="font-semibold">{{ (string) $reply->sender_id === (string) auth()->id() ? 'You' : $reply->sender?->full_name }}</span>
                                            <span class="ml-1 text-slate-500 dark:text-slate-400">{{ $reply->sent_at?->format('H:i') }}</span>
                                            @if ($reply->is_deleted)
                                                <p class="italic text-slate-500 dark:text-slate-400">Message deleted</p>
                                            @else
                                                <p class="mt-0.5 whitespace-pre-wrap break-words text-slate-700 dark:text-slate-200">{{ $reply->body }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-10 text-center text-sm text-slate-500 dark:text-slate-400">No messages yet.</p>
                @endforelse
            </div>
        </div>
    @endif
</div>

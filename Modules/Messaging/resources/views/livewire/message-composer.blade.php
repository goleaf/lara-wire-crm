@if (! $channelId)
    <div class="text-xs text-slate-500 dark:text-slate-400">Select a channel to write a message.</div>
@else
    <form wire:submit.prevent="send" class="space-y-3">
        @if ($replyMessage)
            <div class="flex items-start justify-between rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-xs text-amber-900 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-200">
                <div>
                    <p class="font-semibold">Replying to {{ $replyMessage->sender?->full_name }}</p>
                    <p class="mt-0.5">{{ str($replyMessage->body)->limit(120) }}</p>
                </div>
                <button type="button" wire:click="clearReply" class="ml-3 rounded px-2 py-1 hover:bg-amber-100 dark:hover:bg-amber-800/40">Clear</button>
            </div>
        @endif

        <div class="relative">
            <textarea
                wire:model.live.debounce.300ms="body"
                rows="3"
                placeholder="Write a message... Use @name to mention someone."
                class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:ring-sky-900"
            ></textarea>

            @if ($mentionUsers->isNotEmpty())
                <div class="absolute left-0 top-full z-20 mt-1 w-72 rounded-md border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">
                    @foreach ($mentionUsers as $user)
                        <button
                            type="button"
                            wire:click="insertMention('{{ str($user->full_name)->slug('.') }}')"
                            class="flex w-full items-center justify-between px-3 py-2 text-left text-xs hover:bg-slate-100 dark:hover:bg-slate-800"
                        >
                            <span class="font-medium text-slate-800 dark:text-slate-100">{{ $user->full_name }}</span>
                            <span class="text-slate-500 dark:text-slate-400">{{ $user->email }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div x-data="{ tab: 'smile' }" class="rounded-md border border-slate-200 bg-slate-50 p-2 dark:border-slate-700 dark:bg-slate-800/60">
            <div class="mb-2 flex flex-wrap gap-1 text-[11px]">
                <button type="button" x-on:click="tab = 'smile'" class="rounded px-2 py-1 hover:bg-slate-200 dark:hover:bg-slate-700">Smileys</button>
                <button type="button" x-on:click="tab = 'hand'" class="rounded px-2 py-1 hover:bg-slate-200 dark:hover:bg-slate-700">Gestures</button>
                <button type="button" x-on:click="tab = 'work'" class="rounded px-2 py-1 hover:bg-slate-200 dark:hover:bg-slate-700">Work</button>
                <button type="button" x-on:click="tab = 'travel'" class="rounded px-2 py-1 hover:bg-slate-200 dark:hover:bg-slate-700">Travel</button>
                <button type="button" x-on:click="tab = 'objects'" class="rounded px-2 py-1 hover:bg-slate-200 dark:hover:bg-slate-700">Objects</button>
                <button type="button" x-on:click="tab = 'symbols'" class="rounded px-2 py-1 hover:bg-slate-200 dark:hover:bg-slate-700">Symbols</button>
            </div>
            <div class="flex flex-wrap gap-1 text-base">
                <template x-if="tab === 'smile'">
                    <div class="flex gap-1">
                        <button type="button" wire:click="addEmoji('😀')">😀</button><button type="button" wire:click="addEmoji('🙂')">🙂</button><button type="button" wire:click="addEmoji('😂')">😂</button>
                    </div>
                </template>
                <template x-if="tab === 'hand'">
                    <div class="flex gap-1">
                        <button type="button" wire:click="addEmoji('👍')">👍</button><button type="button" wire:click="addEmoji('👏')">👏</button><button type="button" wire:click="addEmoji('🙏')">🙏</button>
                    </div>
                </template>
                <template x-if="tab === 'work'">
                    <div class="flex gap-1">
                        <button type="button" wire:click="addEmoji('💼')">💼</button><button type="button" wire:click="addEmoji('📈')">📈</button><button type="button" wire:click="addEmoji('✅')">✅</button>
                    </div>
                </template>
                <template x-if="tab === 'travel'">
                    <div class="flex gap-1">
                        <button type="button" wire:click="addEmoji('✈️')">✈️</button><button type="button" wire:click="addEmoji('🚗')">🚗</button><button type="button" wire:click="addEmoji('🏨')">🏨</button>
                    </div>
                </template>
                <template x-if="tab === 'objects'">
                    <div class="flex gap-1">
                        <button type="button" wire:click="addEmoji('📎')">📎</button><button type="button" wire:click="addEmoji('📝')">📝</button><button type="button" wire:click="addEmoji('📌')">📌</button>
                    </div>
                </template>
                <template x-if="tab === 'symbols'">
                    <div class="flex gap-1">
                        <button type="button" wire:click="addEmoji('⭐')">⭐</button><button type="button" wire:click="addEmoji('⚠️')">⚠️</button><button type="button" wire:click="addEmoji('❤️')">❤️</button>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3">
            <div class="flex-1">
                <input
                    type="file"
                    wire:model="uploads"
                    multiple
                    class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-200 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-slate-700 hover:file:bg-slate-300 dark:text-slate-300 dark:file:bg-slate-700 dark:file:text-slate-100"
                >
                @if ($uploads)
                    <div class="mt-1 flex flex-wrap gap-1 text-[11px] text-slate-500 dark:text-slate-400">
                        @foreach ($uploads as $upload)
                            <span class="rounded bg-slate-100 px-1.5 py-0.5 dark:bg-slate-800">{{ $upload->getClientOriginalName() }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <button
                type="submit"
                class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500"
            >
                Send
            </button>
        </div>
    </form>
@endif

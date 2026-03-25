<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-full bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.18),_transparent_42%),linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_48%,_#e2e8f0_100%)] text-slate-950 dark:bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.2),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#0f172a_45%,_#111827_100%)] dark:text-slate-100">
        @php
            $user = auth()->user();
            $content = trim($__env->yieldContent('content'));
            $routeId = (string) request()->route('id');
            $activityRelatedType = '';

            if ($routeId !== '') {
                if (request()->routeIs('contacts.*')) {
                    $activityRelatedType = \Modules\Contacts\Models\Contact::class;
                } elseif (request()->routeIs('accounts.*')) {
                    $activityRelatedType = \Modules\Contacts\Models\Account::class;
                } elseif (request()->routeIs('deals.*')) {
                    $activityRelatedType = \Modules\Deals\Models\Deal::class;
                } elseif (request()->routeIs('leads.*')) {
                    $activityRelatedType = \Modules\Leads\Models\Lead::class;
                } elseif (request()->routeIs('cases.*')) {
                    $activityRelatedType = \Modules\Cases\Models\SupportCase::class;
                } elseif (request()->routeIs('campaigns.*')) {
                    $activityRelatedType = \Modules\Campaigns\Models\Campaign::class;
                }
            }

            $dealCreateParams = [];
            $contactCreateParams = [];
            $caseCreateParams = [];

            if ($routeId !== '' && request()->routeIs('contacts.*')) {
                $dealCreateParams['contact_id'] = $routeId;
                $caseCreateParams['contact_id'] = $routeId;
            }
            if ($routeId !== '' && request()->routeIs('accounts.*')) {
                $dealCreateParams['account_id'] = $routeId;
                $contactCreateParams['account_id'] = $routeId;
                $caseCreateParams['account_id'] = $routeId;
            }
            if ($routeId !== '' && request()->routeIs('deals.*')) {
                $caseCreateParams['deal_id'] = $routeId;
            }
        @endphp

        <div
            x-data="{ sidebarCollapsed: localStorage.getItem('crm.sidebarCollapsed') === '1', quickAddOpen: false }"
            class="min-h-screen lg:grid"
            :class="sidebarCollapsed ? 'lg:grid-cols-[6rem_minmax(0,1fr)]' : 'lg:grid-cols-[18rem_minmax(0,1fr)]'"
        >
            <aside class="hidden border-r border-white/50 bg-white/80 py-8 backdrop-blur lg:flex lg:flex-col dark:border-white/10 dark:bg-slate-950/55" :class="sidebarCollapsed ? 'px-3' : 'px-6'">
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-600 dark:text-sky-300" x-show="!sidebarCollapsed">CRM</p>
                        <button
                            type="button"
                            x-on:click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('crm.sidebarCollapsed', sidebarCollapsed ? '1' : '0')"
                            class="inline-flex size-8 items-center justify-center rounded-lg border border-white/70 bg-white/80 text-slate-500 hover:text-sky-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300"
                            aria-label="Toggle sidebar"
                        >
                            <span x-show="!sidebarCollapsed">◀</span>
                            <span x-show="sidebarCollapsed">▶</span>
                        </button>
                    </div>
                    <div x-show="!sidebarCollapsed">
                        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ config('crm.app_name', config('app.name')) }}</h1>
                    </div>
                </div>

                <nav class="mt-8 flex-1 space-y-4">
                    @forelse ($crmNavigationGroups ?? [] as $group)
                        <section class="space-y-2">
                            <p x-show="!sidebarCollapsed" class="px-1 text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500">{{ $group['group'] }}</p>
                            <div class="space-y-2">
                                @foreach ($group['items'] as $item)
                                    <a
                                        href="{{ $item['href'] }}"
                                        wire:navigate
                                        class="{{ $item['active'] ? 'border-sky-500/60 bg-sky-500 text-white shadow-lg shadow-sky-500/20 dark:border-sky-400/40 dark:bg-sky-500/90' : 'border-white/60 bg-white/70 text-slate-600 hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:border-sky-400/30 dark:hover:text-sky-200' }} flex items-center gap-3 rounded-2xl border px-3 py-2.5 text-sm font-medium transition"
                                        :class="sidebarCollapsed ? 'justify-center' : ''"
                                        title="{{ $item['label'] }}"
                                    >
                                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-black/10 text-xs font-semibold uppercase tracking-[0.2em] text-current dark:bg-white/10">
                                            {{ $item['initials'] ?? strtoupper(substr((string) ($item['label'] ?? ''), 0, 1)) }}
                                        </span>
                                        <span x-show="!sidebarCollapsed">{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300/80 px-4 py-5 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            No enabled CRM modules expose a dashboard route yet.
                        </div>
                    @endforelse
                </nav>

                <div x-show="!sidebarCollapsed" class="rounded-3xl border border-white/60 bg-white/70 p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Defaults</p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-slate-500 dark:text-slate-400">Currency</dt>
                            <dd class="font-semibold text-slate-900 dark:text-white">{{ crm_currency() }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-slate-500 dark:text-slate-400">Timezone</dt>
                            <dd class="font-semibold text-slate-900 dark:text-white">{{ config('crm.timezone') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-slate-500 dark:text-slate-400">Page size</dt>
                            <dd class="font-semibold text-slate-900 dark:text-white">{{ config('crm.pagination_size') }}</dd>
                        </div>
                    </dl>
                </div>
            </aside>

            <div class="flex min-h-screen flex-col">
                <header class="border-b border-white/60 bg-white/75 px-4 py-4 backdrop-blur md:px-8 dark:border-white/10 dark:bg-slate-950/45">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 lg:hidden">
                                <span class="inline-flex rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-sky-700 dark:border-sky-400/30 dark:bg-sky-400/10 dark:text-sky-200">CRM</span>
                                <span class="text-sm text-slate-500 dark:text-slate-400">{{ config('crm.app_name', config('app.name')) }}</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Workspace</p>
                                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $title ?? 'Dashboard' }}</h2>
                            </div>
                            <x-breadcrumbs />
                        </div>

                        <div class="flex w-full max-w-4xl items-center gap-3 lg:justify-end">
                            @island(name: 'layout-top-widgets', defer: true)
                                @placeholder
                                    <div class="flex w-full items-center gap-3 lg:max-w-xl">
                                        <div class="h-11 flex-1 animate-pulse rounded-xl border border-slate-200 bg-slate-100/80 dark:border-slate-800 dark:bg-slate-900/70"></div>
                                        <div class="size-11 animate-pulse rounded-2xl border border-slate-200 bg-slate-100/80 dark:border-slate-800 dark:bg-slate-900/70"></div>
                                    </div>
                                @endplaceholder

                                <div class="flex w-full items-center gap-3 lg:max-w-xl">
                                    @if (class_exists(\Modules\Core\Livewire\GlobalSearch::class))
                                        @livewire(\Modules\Core\Livewire\GlobalSearch::class)
                                    @endif

                                    @if (class_exists(\Modules\Notifications\Livewire\NotificationBell::class))
                                        @livewire(\Modules\Notifications\Livewire\NotificationBell::class)
                                    @endif
                                </div>
                            @endisland

                            @if ($user)
                                <div class="relative" x-data="{ open: false }">
                                    <button
                                        type="button"
                                        x-on:click="open = !open"
                                        class="flex items-center gap-3 rounded-2xl border border-white/70 bg-white/80 px-3 py-2 shadow-sm dark:border-white/10 dark:bg-white/5"
                                    >
                                        <span class="flex size-11 items-center justify-center overflow-hidden rounded-2xl bg-slate-950 text-sm font-semibold text-white dark:bg-sky-400 dark:text-slate-950">
                                            @if ($user->avatar_path)
                                                <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="Avatar" class="size-full object-cover" />
                                            @else
                                                {{ $user->initials() }}
                                            @endif
                                        </span>
                                        <div class="min-w-0 text-left" x-show="!sidebarCollapsed">
                                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                        </div>
                                        <span class="text-xs text-slate-500">▾</span>
                                    </button>

                                    <div
                                        x-cloak
                                        x-show="open"
                                        x-on:click.outside="open = false"
                                        class="absolute right-0 top-14 z-50 w-48 rounded-xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-800 dark:bg-slate-950"
                                    >
                                        <a href="{{ Route::has('users.profile') ? route('users.profile') : route('profile.edit') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900">Profile</a>
                                        @if (Route::has('core.settings'))
                                            <a href="{{ route('core.settings') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900">Settings</a>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="mt-1 w-full rounded-lg px-3 py-2 text-left text-sm text-rose-700 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-500/10">Logout</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2 overflow-x-auto pb-1 lg:hidden">
                        @forelse ($crmNavigation ?? [] as $item)
                            <a
                                href="{{ $item['href'] }}"
                                wire:navigate
                                class="{{ $item['active'] ? 'bg-slate-950 text-white dark:bg-sky-300 dark:text-slate-950' : 'bg-white text-slate-600 dark:bg-white/5 dark:text-slate-300' }} inline-flex items-center gap-2 rounded-full border border-white/70 px-4 py-2 text-sm font-medium shadow-sm dark:border-white/10"
                            >
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @empty
                            <span class="text-sm text-slate-500 dark:text-slate-400">No modules available.</span>
                        @endforelse
                    </div>
                </header>

                <main class="flex-1 px-4 py-6 md:px-8 md:py-8">
                    @if ($content !== '')
                        {!! $content !!}
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>
            </div>

            <div class="fixed bottom-6 right-6 z-[90]">
                <div class="relative">
                    <button
                        type="button"
                        x-on:click="quickAddOpen = !quickAddOpen"
                        class="inline-flex size-14 items-center justify-center rounded-full bg-sky-600 text-2xl font-semibold text-white shadow-lg shadow-sky-600/30 hover:bg-sky-500"
                        aria-label="Quick add"
                    >
                        +
                    </button>

                    <div
                        x-cloak
                        x-show="quickAddOpen"
                        x-on:click.outside="quickAddOpen = false"
                        class="absolute bottom-16 right-0 w-56 space-y-1 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-800 dark:bg-slate-950"
                    >
                        @if (Route::has('leads.create'))
                            <a href="{{ route('leads.create') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900">New Lead</a>
                        @endif
                        @if (Route::has('deals.create'))
                            <a href="{{ route('deals.create', $dealCreateParams) }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900">New Deal</a>
                        @endif
                        @if (Route::has('contacts.create'))
                            <a href="{{ route('contacts.create', $contactCreateParams) }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900">New Contact</a>
                        @endif
                        @if (class_exists(\Modules\Activities\Livewire\QuickAddActivity::class))
                            <button
                                type="button"
                                x-on:click="$dispatch('quick-add-activity', { relatedType: '{{ $activityRelatedType }}', relatedId: '{{ $routeId }}' }); quickAddOpen = false"
                                class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900"
                            >
                                New Activity
                            </button>
                        @elseif (Route::has('activities.create'))
                            <a href="{{ route('activities.create') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900">New Activity</a>
                        @endif
                        @if (Route::has('cases.create'))
                            <a href="{{ route('cases.create', $caseCreateParams) }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900">New Case</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @island(name: 'layout-quick-add', defer: true)
            @placeholder
                <div></div>
            @endplaceholder

            @if (class_exists(\Modules\Activities\Livewire\QuickAddActivity::class))
                @livewire(\Modules\Activities\Livewire\QuickAddActivity::class)
            @endif
        @endisland

        @island(name: 'layout-flash-messages', defer: true)
            @placeholder
                <div></div>
            @endplaceholder

            @if (class_exists(\Modules\Core\Livewire\FlashMessage::class))
                @livewire(\Modules\Core\Livewire\FlashMessage::class)
            @endif
        @endisland

        @island(name: 'layout-confirm-modal', defer: true)
            @placeholder
                <div></div>
            @endplaceholder

            @if (class_exists(\Modules\Core\Livewire\ConfirmModal::class))
                @livewire(\Modules\Core\Livewire\ConfirmModal::class)
            @endif
        @endisland

        @stack('modals')
        @stack('scripts')
        @fluxScripts
    </body>
</html>

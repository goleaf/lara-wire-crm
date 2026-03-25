<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-full bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.18),_transparent_42%),linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_48%,_#e2e8f0_100%)] text-slate-950 dark:bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.2),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#0f172a_45%,_#111827_100%)] dark:text-slate-100">
        @php($user = auth()->user())
        @php($content = trim($__env->yieldContent('content')))

        <div class="min-h-screen lg:grid lg:grid-cols-[18rem_minmax(0,1fr)]">
            <aside class="hidden border-r border-white/50 bg-white/80 px-6 py-8 backdrop-blur lg:flex lg:flex-col dark:border-white/10 dark:bg-slate-950/55">
                <div class="space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-600 dark:text-sky-300">CRM</p>
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ config('crm.app_name', config('app.name')) }}</h1>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Modular workspace powered by Laravel 13, Livewire 4, and Tailwind CSS 4.</p>
                    </div>
                </div>

                <nav class="mt-10 flex-1 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">Modules</p>

                    @forelse ($crmNavigation ?? [] as $item)
                        <a
                            href="{{ $item['href'] }}"
                            class="{{ $item['active'] ? 'border-sky-500/60 bg-sky-500 text-white shadow-lg shadow-sky-500/20 dark:border-sky-400/40 dark:bg-sky-500/90' : 'border-white/60 bg-white/70 text-slate-600 hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:border-sky-400/30 dark:hover:text-sky-200' }} flex items-center gap-3 rounded-2xl border px-4 py-3 text-sm font-medium transition"
                        >
                            <span class="flex size-9 items-center justify-center rounded-xl bg-black/10 text-xs font-semibold uppercase tracking-[0.2em] text-current dark:bg-white/10">
                                {{ $item['initials'] }}
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300/80 px-4 py-5 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            No enabled CRM modules expose a dashboard route yet.
                        </div>
                    @endforelse
                </nav>

                <div class="rounded-3xl border border-white/60 bg-white/70 p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
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
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 lg:hidden">
                                <span class="inline-flex rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-sky-700 dark:border-sky-400/30 dark:bg-sky-400/10 dark:text-sky-200">CRM</span>
                                <span class="text-sm text-slate-500 dark:text-slate-400">{{ config('crm.app_name', config('app.name')) }}</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Workspace</p>
                                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $title ?? 'Dashboard' }}</h2>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 sm:justify-end">
                            <button type="button" class="relative inline-flex size-11 items-center justify-center rounded-2xl border border-white/70 bg-white/80 text-slate-600 shadow-sm transition hover:text-sky-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:text-sky-200" aria-label="Notifications">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17H5.143A1.143 1.143 0 0 1 4 15.857V14.43a3.429 3.429 0 0 1 1.004-2.425l.425-.426A2.286 2.286 0 0 0 6.286 9.96V8.857a5.714 5.714 0 1 1 11.428 0V9.96c0 .607.241 1.19.67 1.62l.425.425A3.429 3.429 0 0 1 20 14.43v1.428A1.143 1.143 0 0 1 18.857 17h-4Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.714 17a2.286 2.286 0 1 0 4.572 0" />
                                </svg>
                                <span class="absolute right-3 top-3 size-2.5 rounded-full bg-rose-500"></span>
                            </button>

                            @if ($user)
                                <div class="flex items-center gap-3 rounded-2xl border border-white/70 bg-white/80 px-3 py-2 shadow-sm dark:border-white/10 dark:bg-white/5">
                                    <span class="flex size-11 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold text-white dark:bg-sky-400 dark:text-slate-950">
                                        {{ $user->initials() }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2 overflow-x-auto pb-1 lg:hidden">
                        @forelse ($crmNavigation ?? [] as $item)
                            <a
                                href="{{ $item['href'] }}"
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
        </div>

        @stack('modals')
        @stack('scripts')
        @fluxScripts
    </body>
</html>

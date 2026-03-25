<section class="mx-auto grid w-full max-w-6xl gap-6">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(20rem,1fr)]">
        <article class="overflow-hidden rounded-[2rem] border border-white/60 bg-white/85 p-8 shadow-[0_24px_80px_-32px_rgba(15,23,42,0.45)] backdrop-blur dark:border-white/10 dark:bg-slate-950/45">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div class="max-w-2xl space-y-4">
                    <p class="inline-flex rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                        Core module
                    </p>
                    <div class="space-y-3">
                        <h3 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">CRM Ready</h3>
                        <p class="max-w-xl text-sm leading-7 text-slate-600 dark:text-slate-300">
                            The Laravel 13 foundation is wired for modules, Livewire 4 page components, Tailwind CSS 4, and UUID-based CRM domain models.
                        </p>
                    </div>
                </div>

                <dl class="grid min-w-64 gap-3 rounded-[1.75rem] border border-slate-200/70 bg-slate-50/90 p-5 text-sm dark:border-slate-800 dark:bg-slate-900/90">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">App</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">{{ config('crm.app_name') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">Currency</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">{{ crm_currency() }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">Timezone</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">{{ config('crm.timezone') }}</dd>
                    </div>
                </dl>
            </div>
        </article>

        <aside class="rounded-[2rem] border border-sky-500/20 bg-sky-950 p-6 text-white shadow-[0_18px_45px_-24px_rgba(14,165,233,0.8)]">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-200/70">Next</p>
            <h4 class="mt-3 text-xl font-semibold">Add CRM modules on top of Core</h4>
            <ul class="mt-6 grid gap-3 text-sm text-sky-100/90">
                <li class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Contacts</li>
                <li class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Deals</li>
                <li class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Invoices</li>
            </ul>
        </aside>
    </div>
</section>

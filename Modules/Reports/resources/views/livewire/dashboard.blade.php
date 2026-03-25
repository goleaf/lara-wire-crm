<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $activeDashboard?->name ?? 'Dashboard' }}</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Realtime snapshot of pipeline, support, and revenue performance.</p>
                <p class="mt-2 text-xs font-medium uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">Core • CRM Ready</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="toggleEditMode" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                    {{ $editMode ? 'Done Editing' : 'Edit Dashboard' }}
                </button>
                <a href="{{ route('reports.index') }}" wire:navigate class="rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-500">Manage Reports</a>
            </div>
        </div>
    </x-crm.card>

    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
        @include('reports::widgets.kpi-card', ['label' => 'Total Pipeline Value', 'value' => number_format($kpis['pipeline_value'], 2), 'trend' => null, 'tone' => 'sky'])
        @include('reports::widgets.kpi-card', ['label' => 'Deals Won This Month', 'value' => number_format($kpis['won_this_month'], 2), 'trend' => null, 'tone' => 'emerald'])
        @include('reports::widgets.kpi-card', ['label' => 'Open Cases', 'value' => $kpis['open_cases'], 'trend' => null, 'tone' => 'amber'])
        @include('reports::widgets.kpi-card', ['label' => 'Overdue Activities', 'value' => $kpis['overdue_activities'], 'trend' => null, 'tone' => 'rose'])
        @include('reports::widgets.kpi-card', ['label' => 'Outstanding Invoices', 'value' => number_format($kpis['outstanding_invoices'], 2), 'trend' => null, 'tone' => 'violet'])
        @include('reports::widgets.kpi-card', ['label' => 'Active Leads', 'value' => $kpis['active_leads'], 'trend' => null, 'tone' => 'indigo'])
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($widgets as $widget)
            <article class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 {{ $editMode ? 'border-dashed' : '' }}">
                @if ($editMode)
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Drag handle</p>
                @endif

                @if ($widget->widget_type === 'ReportChart' && $widget->report)
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $widget->title ?: $widget->report->name }}</h4>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $widget->report->module }} • {{ $widget->report->type }}</p>
                    <a href="{{ route('reports.show', $widget->report_id) }}" wire:navigate class="mt-3 inline-flex rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                        Open Report
                    </a>
                @elseif ($widget->widget_type === 'PipelineFunnel')
                    @include('reports::widgets.pipeline-funnel')
                @elseif ($widget->widget_type === 'ActivityFeed')
                    @include('reports::widgets.activity-feed-widget')
                @elseif ($widget->widget_type === 'RecentDeals')
                    @include('reports::widgets.recent-deals-widget')
                @elseif ($widget->widget_type === 'OpenCases')
                    @include('reports::widgets.open-cases-widget')
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $widget->widget_type }} widget</p>
                @endif
            </article>
        @empty
            <article class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
                No widgets configured yet. Add reports and attach them from the Reports list.
            </article>
        @endforelse
    </div>
</section>

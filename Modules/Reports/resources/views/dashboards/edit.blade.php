@extends('core::layouts.module', ['title' => 'Edit Dashboard'])

@section('content')
    <section class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <article class="crm-card p-6">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $dashboard->name }}</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update dashboard metadata and persist widget layout JSON.</p>
        </article>

        <article class="crm-card p-6">
            <form method="POST" action="{{ route('dashboards.update', $dashboard->id) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="space-y-1">
                        <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Name</span>
                        <input type="text" name="name" value="{{ old('name', $dashboard->name) }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    </label>
                    <label class="space-y-1">
                        <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Layout (JSON)</span>
                        <textarea name="layout" rows="6" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 font-mono text-xs dark:border-slate-700 dark:bg-slate-900">{{ json_encode($dashboard->layout ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>
                    </label>
                </div>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="is_public" value="1" @checked($dashboard->is_public)>
                        Public
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="is_default" value="1" @checked($dashboard->is_default)>
                        Default
                    </label>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="crm-btn crm-btn-primary">
                        Save Layout
                    </button>
                </div>
            </form>
        </article>

        <article class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Widget</th>
                        <th class="px-3 py-2">Title</th>
                        <th class="px-3 py-2">Report</th>
                        <th class="px-3 py-2">Position</th>
                        <th class="px-3 py-2">Size</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($dashboard->widgets as $widget)
                        <tr>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->widget_type }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->title ?: '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->report?->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->position_x }}, {{ $widget->position_y }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->width }} x {{ $widget->height }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No widgets configured.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </article>
    </section>
@endsection

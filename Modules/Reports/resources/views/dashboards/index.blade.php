@extends('core::layouts.module', ['title' => 'Dashboards'])

@section('content')
    <section class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <form method="POST" action="{{ route('dashboards.store') }}" class="grid gap-3 md:grid-cols-[1fr_auto]">
                @csrf
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Dashboard Name</span>
                    <input type="text" name="name" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>
                <button type="submit" class="self-end rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-500">
                    Create Dashboard
                </button>
            </form>
        </article>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Owner</th>
                        <th class="px-3 py-2">Visibility</th>
                        <th class="px-3 py-2">Default</th>
                        <th class="px-3 py-2">Updated</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($dashboards as $dashboard)
                        <tr>
                            <td class="px-3 py-2 font-medium text-slate-900 dark:text-slate-100">{{ $dashboard->name }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $dashboard->owner?->full_name ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $dashboard->is_public ? 'Public' : 'Private' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $dashboard->is_default ? 'Yes' : 'No' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $dashboard->updated_at?->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('dashboards.edit', $dashboard->id) }}" class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">
                                    Edit Layout
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No dashboards yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

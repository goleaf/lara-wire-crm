<?php

namespace Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Reports\Models\Dashboard;

class DashboardsController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->can('reports.view'), 403);

        $dashboards = Dashboard::query()
            ->select(['id', 'name', 'owner_id', 'is_default', 'is_public', 'updated_at'])
            ->with(['owner:id,full_name'])
            ->orderByDesc('updated_at')
            ->get();

        return view('reports::dashboards.index', [
            'dashboards' => $dashboards,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->can('reports.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        Dashboard::query()->create([
            'name' => $validated['name'],
            'owner_id' => auth()->id(),
            'is_default' => false,
            'is_public' => (bool) ($validated['is_public'] ?? false),
            'layout' => [],
        ]);

        return redirect()
            ->route('dashboards.index')
            ->with('status', 'Dashboard created.');
    }

    public function edit(string $id): View
    {
        abort_unless(auth()->user()?->can('reports.edit'), 403);

        $dashboard = Dashboard::query()
            ->select(['id', 'name', 'owner_id', 'is_default', 'is_public', 'layout'])
            ->with(['widgets:id,dashboard_id,report_id,widget_type,title,position_x,position_y,width,height,config', 'widgets.report:id,name,type,module'])
            ->findOrFail($id);

        return view('reports::dashboards.edit', [
            'dashboard' => $dashboard,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('reports.edit'), 403);

        $validated = $request->validate([
            'layout' => ['nullable', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
            'is_public' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $parsedLayout = $dashboardLayout = $request->input('layout');

        if (is_string($dashboardLayout) && trim($dashboardLayout) !== '') {
            $parsedLayout = json_decode($dashboardLayout, true);

            if (! is_array($parsedLayout)) {
                return back()->withErrors([
                    'layout' => 'Layout must be valid JSON.',
                ])->withInput();
            }
        } else {
            $parsedLayout = [];
        }

        $dashboard = Dashboard::query()
            ->select(['id', 'name', 'is_public', 'is_default', 'layout'])
            ->findOrFail($id);

        $dashboard->fill([
            'name' => $validated['name'] ?? $dashboard->name,
            'is_public' => (bool) ($validated['is_public'] ?? $dashboard->is_public),
            'is_default' => (bool) ($validated['is_default'] ?? $dashboard->is_default),
            'layout' => $parsedLayout,
        ]);
        $dashboard->save();

        return redirect()
            ->route('dashboards.edit', ['id' => $id])
            ->with('status', 'Dashboard layout updated.');
    }
}

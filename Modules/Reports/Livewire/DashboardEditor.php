<?php

namespace Modules\Reports\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Reports\Models\Dashboard as DashboardModel;

class DashboardEditor extends Component
{
    public string $dashboardId = '';

    public string $name = '';

    public string $layoutJson = '';

    public bool $isPublic = false;

    public bool $isDefault = false;

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('reports.edit'), 403);

        $this->dashboardId = $id;
        $this->loadDashboardState();
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('reports.edit'), 403);

        $validated = $this->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'layoutJson' => ['nullable', 'string'],
            'isPublic' => ['boolean'],
            'isDefault' => ['boolean'],
        ]);

        $layoutInput = trim((string) ($validated['layoutJson'] ?? ''));
        $parsedLayout = [];

        if ($layoutInput !== '') {
            $decodedLayout = json_decode($layoutInput, true);

            if (! is_array($decodedLayout)) {
                $this->addError('layoutJson', 'Layout must be valid JSON.');

                return;
            }

            $parsedLayout = $decodedLayout;
        }

        $dashboard = DashboardModel::query()
            ->select(['id', 'name', 'is_public', 'is_default', 'layout'])
            ->findOrFail($this->dashboardId);

        $dashboard->fill([
            'name' => filled($validated['name'] ?? null) ? (string) $validated['name'] : $dashboard->name,
            'is_public' => (bool) ($validated['isPublic'] ?? false),
            'is_default' => (bool) ($validated['isDefault'] ?? false),
            'layout' => $parsedLayout,
        ]);
        $dashboard->save();

        $this->loadDashboardState();

        session()->flash('status', 'Dashboard layout updated.');
        $this->redirectRoute('dashboards.edit', ['id' => $this->dashboardId], navigate: true);
    }

    public function render(): View
    {
        $dashboard = DashboardModel::query()
            ->select(['id', 'name', 'owner_id', 'is_default', 'is_public', 'layout'])
            ->with([
                'widgets:id,dashboard_id,report_id,widget_type,title,position_x,position_y,width,height,config',
                'widgets.report:id,name,type,module',
            ])
            ->findOrFail($this->dashboardId);

        return view('reports::livewire.dashboard-editor', [
            'dashboard' => $dashboard,
        ])->extends('core::layouts.module', ['title' => 'Edit Dashboard']);
    }

    protected function loadDashboardState(): void
    {
        $dashboard = DashboardModel::query()
            ->select(['id', 'name', 'is_public', 'is_default', 'layout'])
            ->findOrFail($this->dashboardId);

        $this->name = (string) $dashboard->name;
        $this->isPublic = (bool) $dashboard->is_public;
        $this->isDefault = (bool) $dashboard->is_default;
        $this->layoutJson = json_encode($dashboard->layout ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '[]';
    }
}

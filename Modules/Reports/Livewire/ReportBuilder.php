<?php

namespace Modules\Reports\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Reports\Models\Report;
use Modules\Reports\Services\ReportService;

class ReportBuilder extends Component
{
    public ?string $reportId = null;

    public string $name = '';

    public string $description = '';

    public string $type = 'Bar';

    public string $module = 'Deals';

    public string $group_by = '';

    public string $metrics = 'count';

    public string $date_field = 'created_at';

    public string $date_range = 'This Month';

    public string $custom_date_from = '';

    public string $custom_date_to = '';

    public bool $is_public = false;

    /**
     * Key-value filter map encoded in JSON for quick editing.
     */
    public string $filters_json = '{}';

    public function mount(?string $id = null): void
    {
        $this->reportId = $id;

        if ($id !== null) {
            abort_unless(auth()->user()?->can('reports.edit'), 403);
            $this->loadReport($id);

            return;
        }

        abort_unless(auth()->user()?->can('reports.create'), 403);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['Table', 'Bar', 'Line', 'Funnel', 'Pie', 'KPI', 'Area'])],
            'module' => ['required', Rule::in(['Deals', 'Contacts', 'Leads', 'Activities', 'Cases', 'Campaigns', 'Invoices', 'Quotes', 'Products'])],
            'group_by' => ['nullable', 'string', 'max:120'],
            'metrics' => ['required', 'string', 'max:255'],
            'date_field' => ['nullable', 'string', 'max:120'],
            'date_range' => ['required', Rule::in(['Today', 'This Week', 'This Month', 'This Quarter', 'This Year', 'Last 30 Days', 'Last 90 Days', 'Custom'])],
            'custom_date_from' => ['nullable', 'date'],
            'custom_date_to' => ['nullable', 'date', 'after_or_equal:custom_date_from'],
            'is_public' => ['boolean'],
            'filters_json' => ['required', 'string'],
        ]);

        $decodedFilters = json_decode($validated['filters_json'], true);

        if (! is_array($decodedFilters)) {
            $this->addError('filters_json', 'Filters must be a valid JSON object.');

            return;
        }

        $report = Report::query()->updateOrCreate(
            ['id' => $this->reportId],
            [
                'name' => $validated['name'],
                'description' => $this->nullableString($validated['description']),
                'type' => $validated['type'],
                'module' => $validated['module'],
                'filters' => $decodedFilters,
                'group_by' => $this->nullableString($validated['group_by']),
                'metrics' => array_values(array_filter(array_map('trim', explode(',', $validated['metrics'])))),
                'date_field' => $this->nullableString($validated['date_field']),
                'date_range' => $validated['date_range'],
                'custom_date_from' => $this->nullableString($validated['custom_date_from']),
                'custom_date_to' => $this->nullableString($validated['custom_date_to']),
                'is_scheduled' => false,
                'schedule_frequency' => null,
                'owner_id' => auth()->id(),
                'is_public' => (bool) $validated['is_public'],
            ]
        );

        session()->flash('status', 'Report saved.');
        $this->redirectRoute('reports.show', ['id' => $report->id], navigate: true);
    }

    public function render(ReportService $reportService): View
    {
        $previewConfig = null;

        if (filled($this->name)) {
            $previewReport = new Report([
                'name' => $this->name,
                'type' => $this->type,
                'module' => $this->module,
                'filters' => json_decode($this->filters_json, true) ?: [],
                'group_by' => $this->nullableString($this->group_by),
                'metrics' => array_values(array_filter(array_map('trim', explode(',', $this->metrics)))),
                'date_field' => $this->nullableString($this->date_field) ?: 'created_at',
                'date_range' => $this->date_range,
                'custom_date_from' => $this->nullableString($this->custom_date_from),
                'custom_date_to' => $this->nullableString($this->custom_date_to),
            ]);

            try {
                $data = $reportService->getData($previewReport);
                $previewType = match ($this->type) {
                    'Line' => 'line',
                    'Pie' => 'pie',
                    default => 'bar',
                };
                $previewConfig = [
                    'type' => $previewType,
                    'data' => $data,
                    'options' => ['responsive' => true, 'maintainAspectRatio' => false],
                ];
            } catch (\Throwable) {
                $previewConfig = null;
            }
        }

        return view('reports::livewire.report-builder', [
            'dateRanges' => ['Today', 'This Week', 'This Month', 'This Quarter', 'This Year', 'Last 30 Days', 'Last 90 Days', 'Custom'],
            'modules' => ['Deals', 'Contacts', 'Leads', 'Activities', 'Cases', 'Campaigns', 'Invoices', 'Quotes', 'Products'],
            'previewConfig' => $previewConfig,
            'types' => ['Table', 'Bar', 'Line', 'Funnel', 'Pie', 'KPI', 'Area'],
        ])->extends('core::layouts.module', ['title' => $this->reportId ? 'Edit Report' : 'Create Report']);
    }

    protected function loadReport(string $id): void
    {
        $report = Report::query()
            ->select([
                'id',
                'name',
                'description',
                'type',
                'module',
                'filters',
                'group_by',
                'metrics',
                'date_field',
                'date_range',
                'custom_date_from',
                'custom_date_to',
                'is_public',
            ])
            ->findOrFail($id);

        $this->name = (string) $report->name;
        $this->description = (string) ($report->description ?? '');
        $this->type = (string) $report->type;
        $this->module = (string) $report->module;
        $this->group_by = (string) ($report->group_by ?? '');
        $this->metrics = implode(',', (array) $report->metrics);
        $this->date_field = (string) ($report->date_field ?? 'created_at');
        $this->date_range = (string) $report->date_range;
        $this->custom_date_from = $report->custom_date_from?->toDateString() ?? '';
        $this->custom_date_to = $report->custom_date_to?->toDateString() ?? '';
        $this->is_public = (bool) $report->is_public;
        $this->filters_json = json_encode($report->filters ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}

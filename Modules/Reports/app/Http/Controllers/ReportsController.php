<?php

namespace Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Modules\Reports\Models\Report;
use Modules\Reports\Services\ReportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function export(string $id): StreamedResponse
    {
        abort_unless(auth()->user()?->can('reports.export'), 403);

        $report = Report::query()
            ->select(['id', 'name', 'type', 'module', 'filters', 'group_by', 'metrics', 'date_field', 'date_range', 'custom_date_from', 'custom_date_to'])
            ->findOrFail($id);

        $rows = $this->reportService->getTableData($report);
        $columns = $this->resolveCsvColumns($rows);
        $filename = str($report->name)->slug()->append('.csv')->value();

        return response()->streamDownload(function () use ($rows, $columns): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, $columns);

            foreach ($rows as $row) {
                $line = [];

                foreach ($columns as $column) {
                    $line[] = data_get($row, $column);
                }

                fputcsv($handle, $line);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('reports.delete'), 403);

        Report::query()->whereKey($id)->delete();

        return redirect()
            ->route('reports.index')
            ->with('status', 'Report deleted.');
    }

    /**
     * @param  Collection<int, mixed>  $rows
     * @return array<int, string>
     */
    protected function resolveCsvColumns(Collection $rows): array
    {
        $firstRow = $rows->first();

        if ($firstRow === null) {
            return ['id'];
        }

        if (is_array($firstRow)) {
            return array_keys($firstRow);
        }

        if (is_object($firstRow) && method_exists($firstRow, 'getAttributes')) {
            return array_keys($firstRow->getAttributes());
        }

        return ['id'];
    }
}

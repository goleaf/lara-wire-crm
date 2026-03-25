<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Modules\Activities\Models\Activity;
use Modules\Campaigns\Models\Campaign;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Models\Invoice;
use Modules\Leads\Models\Lead;
use Modules\Products\Models\Product;
use Modules\Quotes\Models\Quote;
use Modules\Reports\Models\Report;

class ReportService
{
    public function buildQuery(Report $report): Builder
    {
        $modelClass = $this->resolveModelClass((string) $report->module);

        if (! class_exists($modelClass)) {
            throw new InvalidArgumentException("Unknown report module [{$report->module}].");
        }

        /** @var class-string<Model> $modelClass */
        $query = $modelClass::query();
        $query = $this->applyDateRange($query, $report);

        foreach ((array) $report->filters as $field => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);

                continue;
            }

            $query->where($field, $value);
        }

        return $query;
    }

    /**
     * @return array{labels: array<int, string>, datasets: array<int, array<string, mixed>>}
     */
    public function getData(Report $report): array
    {
        $query = $this->buildQuery($report);
        $groupBy = (string) ($report->group_by ?? '');
        $dateField = (string) ($report->date_field ?: 'created_at');

        $columns = ['id', $dateField];

        if ($groupBy !== '') {
            $columns[] = $groupBy;
        }

        $rows = (clone $query)->select(array_values(array_unique($columns)))->limit(5000)->get();

        if ($groupBy !== '') {
            $grouped = $rows->groupBy(fn ($row): string => (string) data_get($row, $groupBy, 'N/A'));
            $labels = $grouped->keys()->map(fn ($label) => (string) $label)->values()->all();
            $values = $grouped->map(fn (Collection $items): int => $items->count())->values()->all();
        } else {
            $grouped = $rows->groupBy(function ($row) use ($dateField): string {
                $dateValue = data_get($row, $dateField);
                $normalized = $this->normalizeDateValue($dateValue);

                return $normalized ? $normalized->format('Y-m') : 'Unknown';
            });
            $labels = $grouped->keys()->map(fn ($label) => (string) $label)->values()->all();
            $values = $grouped->map(fn (Collection $items): int => $items->count())->values()->all();
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => $report->name,
                'data' => $values,
                'borderColor' => '#0284c7',
                'backgroundColor' => 'rgba(2,132,199,0.2)',
                'tension' => 0.35,
            ]],
        ];
    }

    /**
     * @return Collection<int, Model>
     */
    public function getTableData(Report $report): Collection
    {
        return $this->buildQuery($report)
            ->limit(200)
            ->get();
    }

    public function applyDateRange(Builder $query, Report $report): Builder
    {
        $dateField = (string) ($report->date_field ?: 'created_at');
        $now = now();

        return match ($report->date_range) {
            'Today' => $query->whereDate($dateField, $now->toDateString()),
            'This Week' => $query->whereBetween($dateField, [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]),
            'This Month' => $query->whereBetween($dateField, [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]),
            'This Quarter' => $query->whereBetween($dateField, [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]),
            'This Year' => $query->whereBetween($dateField, [$now->copy()->startOfYear(), $now->copy()->endOfYear()]),
            'Last 30 Days' => $query->whereBetween($dateField, [$now->copy()->subDays(30), $now]),
            'Last 90 Days' => $query->whereBetween($dateField, [$now->copy()->subDays(90), $now]),
            'Custom' => $this->applyCustomRange($query, $dateField, $report),
            default => $query,
        };
    }

    protected function applyCustomRange(Builder $query, string $dateField, Report $report): Builder
    {
        $from = $report->custom_date_from;
        $to = $report->custom_date_to;

        if ($from && $to) {
            return $query->whereBetween($dateField, [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
        }

        if ($from) {
            return $query->where($dateField, '>=', $from->copy()->startOfDay());
        }

        if ($to) {
            return $query->where($dateField, '<=', $to->copy()->endOfDay());
        }

        return $query;
    }

    protected function resolveModelClass(string $module): string
    {
        return match ($module) {
            'Deals' => Deal::class,
            'Contacts' => Contact::class,
            'Leads' => Lead::class,
            'Activities' => Activity::class,
            'Cases' => SupportCase::class,
            'Campaigns' => Campaign::class,
            'Invoices' => Invoice::class,
            'Quotes' => Quote::class,
            'Products' => Product::class,
            default => '',
        };
    }

    protected function normalizeDateValue(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value);
        }

        return null;
    }
}

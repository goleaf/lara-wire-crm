<?php

namespace Modules\Reports\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\Concerns\HasAuditLog;
use Modules\Reports\Database\Factories\ReportFactory;

class Report extends BaseModel
{
    use HasAuditLog;
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
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
        'is_scheduled',
        'schedule_frequency',
        'owner_id',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'metrics' => 'array',
            'custom_date_from' => 'date',
            'custom_date_to' => 'date',
            'is_scheduled' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class, 'report_id');
    }

    protected static function newFactory(): ReportFactory
    {
        return ReportFactory::new();
    }
}

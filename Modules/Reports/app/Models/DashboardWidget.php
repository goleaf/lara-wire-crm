<?php

namespace Modules\Reports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Reports\Database\Factories\DashboardWidgetFactory;

class DashboardWidget extends BaseModel
{
    use HasFactory;

    protected $table = 'dashboard_widgets';

    protected $fillable = [
        'dashboard_id',
        'report_id',
        'widget_type',
        'title',
        'position_x',
        'position_y',
        'width',
        'height',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'position_x' => 'integer',
            'position_y' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'config' => 'array',
        ];
    }

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class, 'dashboard_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    protected static function newFactory(): DashboardWidgetFactory
    {
        return DashboardWidgetFactory::new();
    }
}

<?php

namespace Modules\Reports\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;
use Modules\Reports\Database\Factories\DashboardFactory;

class Dashboard extends BaseModel
{
    use HasFactory;

    protected $table = 'dashboards';

    protected $fillable = [
        'name',
        'owner_id',
        'is_default',
        'is_public',
        'layout',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_public' => 'boolean',
            'layout' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class, 'dashboard_id');
    }

    protected static function newFactory(): DashboardFactory
    {
        return DashboardFactory::new();
    }
}

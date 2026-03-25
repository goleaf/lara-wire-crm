<?php

namespace Modules\Deals\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;
use Modules\Deals\Database\Factories\PipelineFactory;

class Pipeline extends BaseModel
{
    use HasFactory;

    protected $table = 'pipelines';

    protected $fillable = [
        'name',
        'is_default',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(PipelineStage::class, 'pipeline_id')->orderBy('order');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'pipeline_id');
    }

    public static function getDefault(): Pipeline
    {
        return static::query()->where('is_default', true)->firstOrFail();
    }

    protected static function newFactory(): PipelineFactory
    {
        return PipelineFactory::new();
    }
}

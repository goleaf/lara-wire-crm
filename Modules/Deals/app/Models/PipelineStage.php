<?php

namespace Modules\Deals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;
use Modules\Deals\Database\Factories\PipelineStageFactory;

class PipelineStage extends BaseModel
{
    use HasFactory;

    protected $table = 'pipeline_stages';

    protected $fillable = [
        'pipeline_id',
        'name',
        'order',
        'probability',
        'color',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'probability' => 'integer',
        ];
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class, 'pipeline_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'stage_id');
    }

    protected static function newFactory(): PipelineStageFactory
    {
        return PipelineStageFactory::new();
    }
}

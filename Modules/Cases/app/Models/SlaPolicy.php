<?php

namespace Modules\Cases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cases\Database\Factories\SlaPolicyFactory;
use Modules\Core\Models\BaseModel;

class SlaPolicy extends BaseModel
{
    use HasFactory;

    protected $table = 'sla_policies';

    protected $fillable = [
        'name',
        'priority',
        'first_response_hours',
        'resolution_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'first_response_hours' => 'integer',
            'resolution_hours' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function forPriority(string $priority): ?self
    {
        return self::query()
            ->select(['id', 'priority', 'first_response_hours', 'resolution_hours', 'is_active'])
            ->where('priority', $priority)
            ->where('is_active', true)
            ->first();
    }

    protected static function newFactory(): SlaPolicyFactory
    {
        return SlaPolicyFactory::new();
    }
}

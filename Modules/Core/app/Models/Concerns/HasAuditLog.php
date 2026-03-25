<?php

namespace Modules\Core\Models\Concerns;

use Modules\Core\Models\AuditLog;

trait HasAuditLog
{
    public static function bootHasAuditLog(): void
    {
        static::created(function ($model): void {
            static::writeAuditLog($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model): void {
            $changes = $model->getChanges();

            if ($changes === []) {
                return;
            }

            $oldValues = array_intersect_key($model->getOriginal(), $changes);

            static::writeAuditLog($model, 'updated', $oldValues, $changes);
        });

        static::deleted(function ($model): void {
            static::writeAuditLog($model, 'deleted', $model->getOriginal(), null);
        });
    }

    protected static function writeAuditLog(mixed $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        if (! class_exists(AuditLog::class) || $model instanceof AuditLog) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model::class,
            'model_id' => (string) $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
        ]);
    }
}

<?php

namespace Modules\Core\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            $keyName = $model->getKeyName();

            if (blank($model->getAttribute($keyName))) {
                $model->setAttribute($keyName, Str::uuid()->toString());
            }
        });
    }
}

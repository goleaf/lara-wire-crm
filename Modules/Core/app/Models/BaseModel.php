<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Concerns\HasUuid;

abstract class BaseModel extends Model
{
    use HasUuid;

    protected $keyType = 'string';

    public $incrementing = false;
}

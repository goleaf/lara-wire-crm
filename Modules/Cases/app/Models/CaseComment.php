<?php

namespace Modules\Cases\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Cases\Database\Factories\CaseCommentFactory;
use Modules\Core\Models\BaseModel;

class CaseComment extends BaseModel
{
    use HasFactory;

    protected $table = 'case_comments';

    protected $fillable = [
        'case_id',
        'user_id',
        'body',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    public function supportCase(): BelongsTo
    {
        return $this->belongsTo(SupportCase::class, 'case_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function newFactory(): CaseCommentFactory
    {
        return CaseCommentFactory::new();
    }
}

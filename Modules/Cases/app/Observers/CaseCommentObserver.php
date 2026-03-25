<?php

namespace Modules\Cases\Observers;

use Modules\Cases\Models\CaseComment;

class CaseCommentObserver
{
    public function created(CaseComment $comment): void
    {
        $supportCase = $comment->supportCase;

        if (! $supportCase || $supportCase->first_response_at !== null) {
            return;
        }

        $supportCase->forceFill([
            'first_response_at' => now(),
        ])->saveQuietly();
    }
}

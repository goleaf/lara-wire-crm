<?php

namespace Modules\Users\Listeners;

use Illuminate\Auth\Events\Login;

class RecordLastLogin
{
    public function handle(Login $event): void
    {
        $event->user->forceFill([
            'last_login' => now(),
        ])->save();
    }
}

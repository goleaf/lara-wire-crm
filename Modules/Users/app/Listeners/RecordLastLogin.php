<?php

namespace Modules\Users\Listeners;

use Modules\Users\app\Events\Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordLastLogin
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void {}
}

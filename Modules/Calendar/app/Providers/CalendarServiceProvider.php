<?php

namespace Modules\Calendar\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Calendar\Models\CalendarEvent;
use Modules\Calendar\Observers\CalendarEventObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CalendarServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Calendar';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'calendar';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        CalendarEvent::observe(CalendarEventObserver::class);

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("calendar.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('calendar'));
        }
    }
}

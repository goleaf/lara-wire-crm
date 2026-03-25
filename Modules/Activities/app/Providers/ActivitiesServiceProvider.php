<?php

namespace Modules\Activities\Providers;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Modules\Activities\Models\Activity;
use Modules\Activities\Observers\ActivityObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class ActivitiesServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Activities';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'activities';

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

        Activity::observe(ActivityObserver::class);

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("activities.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('activities'));
        }
    }

    /**
     * Define module schedules.
     *
     * @param  $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}

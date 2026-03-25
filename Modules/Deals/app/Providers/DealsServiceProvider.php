<?php

namespace Modules\Deals\Providers;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Modules\Deals\Models\Deal;
use Modules\Deals\Observers\DealObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class DealsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Deals';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'deals';

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

        Deal::observe(DealObserver::class);

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("deals.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('deals'));
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

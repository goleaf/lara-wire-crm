<?php

namespace Modules\Leads\Providers;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Modules\Leads\Models\Lead;
use Modules\Leads\Observers\LeadObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class LeadsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Leads';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'leads';

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

        Lead::observe(LeadObserver::class);

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("leads.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('leads'));
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

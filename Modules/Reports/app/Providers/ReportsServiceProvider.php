<?php

namespace Modules\Reports\Providers;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Nwidart\Modules\Support\ModuleServiceProvider;

class ReportsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Reports';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'reports';

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

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define(
                "reports.{$action}",
                fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('reports')
            );
        }
    }

    protected function configureSchedules(Schedule $schedule): void
    {
        // Schedules can be added here for report cache refresh jobs.
    }
}

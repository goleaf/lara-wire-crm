<?php

namespace Modules\Notifications\Providers;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Modules\Notifications\Console\CheckActivityReminders;
use Nwidart\Modules\Support\ModuleServiceProvider;

class NotificationsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Notifications';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'notifications';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        CheckActivityReminders::class,
    ];

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
            Gate::define("notifications.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('notifications'));
        }
    }

    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('notifications:check-activity-reminders')->everyFifteenMinutes();
    }
}

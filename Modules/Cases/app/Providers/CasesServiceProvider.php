<?php

namespace Modules\Cases\Providers;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Modules\Cases\Console\CheckSlaCases;
use Modules\Cases\Models\CaseComment;
use Modules\Cases\Models\SupportCase;
use Modules\Cases\Observers\CaseCommentObserver;
use Modules\Cases\Observers\SupportCaseObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CasesServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Cases';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'cases';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        CheckSlaCases::class,
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

        SupportCase::observe(SupportCaseObserver::class);
        CaseComment::observe(CaseCommentObserver::class);

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("cases.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('cases'));
        }
    }

    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('cases:check-sla')->everyThirtyMinutes();
    }
}

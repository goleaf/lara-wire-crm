<?php

namespace Modules\Invoices\Providers;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Modules\Invoices\Console\CheckOverdueInvoices;
use Modules\Invoices\Models\Payment;
use Modules\Invoices\Observers\PaymentObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class InvoicesServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Invoices';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'invoices';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        CheckOverdueInvoices::class,
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

        Payment::observe(PaymentObserver::class);

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("invoices.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('invoices'));
        }
    }

    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('invoices:check-overdue')->daily();
    }
}

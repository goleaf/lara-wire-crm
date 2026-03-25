<?php

namespace Modules\Contacts\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Nwidart\Modules\Support\ModuleServiceProvider;

class ContactsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Contacts';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'contacts';

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

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("contacts.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('contacts'));
        }
    }
}

<?php

namespace Modules\Users\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Users\Http\Middleware\EnsureUserIsActive;
use Modules\Users\Http\Middleware\PermissionMiddleware;
use Nwidart\Modules\Support\ModuleServiceProvider;

class UsersServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Users';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'users';

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

        $router = $this->app['router'];
        $router->aliasMiddleware('active', EnsureUserIsActive::class);
        $router->aliasMiddleware('permission', PermissionMiddleware::class);

        $actions = ['view', 'create', 'edit', 'delete', 'export'];

        foreach ($actions as $action) {
            Gate::define($action, fn (User $user): bool => $user->hasPermission($action));
            Gate::define("users.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('users'));
        }
    }
}

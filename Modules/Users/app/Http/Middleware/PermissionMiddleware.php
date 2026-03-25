<?php

namespace Modules\Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ?string $action = null,
        ?string $module = null
    ): Response|SymfonyResponse {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $routeName = (string) $request->route()?->getName();
        $resolvedAction = $action ?? $this->resolveActionFromRoute($routeName, $request->method());
        $resolvedModule = Str::lower($module ?? Str::before($routeName, '.'));

        if (! $user->hasPermission($resolvedAction)) {
            abort(403);
        }

        if ($resolvedModule !== '' && ! $user->canAccessModule($resolvedModule)) {
            abort(403);
        }

        return $next($request);
    }

    protected function resolveActionFromRoute(string $routeName, string $method): string
    {
        if (Str::endsWith($routeName, '.destroy') || $method === 'DELETE') {
            return 'delete';
        }

        if (Str::endsWith($routeName, '.create') || Str::endsWith($routeName, '.store')) {
            return 'create';
        }

        if (Str::endsWith($routeName, '.edit') || Str::endsWith($routeName, '.update')) {
            return 'edit';
        }

        if (Str::contains($routeName, 'export')) {
            return 'export';
        }

        return 'view';
    }
}

<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Nwidart\Modules\Facades\Module;

class AppLayoutComposer
{
    public function compose(View $view): void
    {
        $navigation = collect(Module::allEnabled())
            ->map(function ($module): ?array {
                $alias = $module->getLowerName();
                $routeName = $alias === 'core' ? 'dashboard' : "{$alias}.dashboard";

                if (! Route::has($routeName)) {
                    return null;
                }

                return [
                    'active' => request()->routeIs($routeName) || request()->routeIs("{$alias}.*"),
                    'href' => route($routeName),
                    'initials' => strtoupper(substr($module->getName(), 0, 2)),
                    'label' => $module->getName(),
                ];
            })
            ->filter()
            ->values()
            ->all();

        $view->with('crmNavigation', $navigation);
    }
}

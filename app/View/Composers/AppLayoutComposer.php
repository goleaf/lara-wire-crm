<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AppLayoutComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();
        $isAdminOrManager = in_array(strtolower((string) $user?->role?->name), ['admin', 'manager'], true);

        $groups = [
            'Sales' => [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Leads', 'route' => 'leads.index'],
                ['label' => 'Deals', 'route' => 'deals.index'],
                ['label' => 'Contacts', 'route' => 'contacts.index'],
                ['label' => 'Accounts', 'route' => 'accounts.index'],
            ],
            'Work' => [
                ['label' => 'Activities', 'route' => 'activities.mine'],
                ['label' => 'Calendar', 'route' => 'calendar.index'],
                ['label' => 'Messages', 'route' => 'messages.index'],
            ],
            'Finance' => [
                ['label' => 'Quotes', 'route' => 'quotes.index'],
                ['label' => 'Invoices', 'route' => 'invoices.index'],
            ],
            'Marketing' => [
                ['label' => 'Campaigns', 'route' => 'campaigns.index'],
            ],
            'Support' => [
                ['label' => 'Cases', 'route' => 'cases.index'],
            ],
            'Admin' => $isAdminOrManager ? [
                ['label' => 'Products', 'route' => 'products.index'],
                ['label' => 'Reports', 'route' => 'reports.index'],
                ['label' => 'Users', 'route' => 'users.index'],
                ['label' => 'Roles', 'route' => 'roles.index'],
                ['label' => 'Teams', 'route' => 'teams.index'],
                ['label' => 'Files', 'route' => 'files.index'],
                ['label' => 'Settings', 'route' => 'core.settings'],
                ['label' => 'Audit Logs', 'route' => 'core.audit-logs'],
            ] : [],
        ];

        $navigationGroups = collect($groups)
            ->map(function (array $items, string $groupLabel): array {
                $resolvedItems = collect($items)
                    ->filter(fn (array $item): bool => Route::has($item['route']))
                    ->map(function (array $item): array {
                        $pattern = str_contains($item['route'], '.')
                            ? str_replace('.index', '.*', $item['route'])
                            : $item['route'];

                        return [
                            'active' => request()->routeIs($pattern),
                            'href' => route($item['route']),
                            'initials' => Str::of($item['label'])
                                ->explode(' ')
                                ->take(2)
                                ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
                                ->implode(''),
                            'label' => $item['label'],
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'group' => $groupLabel,
                    'items' => $resolvedItems,
                ];
            })
            ->filter(fn (array $group): bool => $group['items'] !== [])
            ->values()
            ->all();

        $flatNavigation = collect($navigationGroups)
            ->flatMap(fn (array $group): array => $group['items'])
            ->values()
            ->all();

        $view->with('crmNavigationGroups', $navigationGroups);
        $view->with('crmNavigation', $flatNavigation);
    }
}

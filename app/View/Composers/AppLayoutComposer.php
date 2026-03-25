<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AppLayoutComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();
        $isAdminOrManager = in_array(strtolower((string) $user?->role?->name), ['admin', 'manager'], true);

        $groups = [
            'Sales' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fa-solid fa-gauge-high'],
                ['label' => 'Leads', 'route' => 'leads.index', 'icon' => 'fa-solid fa-bullseye'],
                ['label' => 'Deals', 'route' => 'deals.index', 'icon' => 'fa-solid fa-handshake'],
                ['label' => 'Contacts', 'route' => 'contacts.index', 'icon' => 'fa-solid fa-address-book'],
                ['label' => 'Accounts', 'route' => 'accounts.index', 'icon' => 'fa-solid fa-building'],
            ],
            'Work' => [
                ['label' => 'Activities', 'route' => 'activities.mine', 'icon' => 'fa-solid fa-list-check'],
                ['label' => 'Calendar', 'route' => 'calendar.index', 'icon' => 'fa-solid fa-calendar-days'],
                ['label' => 'Messages', 'route' => 'messages.index', 'icon' => 'fa-solid fa-comments'],
            ],
            'Finance' => [
                ['label' => 'Quotes', 'route' => 'quotes.index', 'icon' => 'fa-solid fa-file-signature'],
                ['label' => 'Invoices', 'route' => 'invoices.index', 'icon' => 'fa-solid fa-file-invoice-dollar'],
            ],
            'Marketing' => [
                ['label' => 'Campaigns', 'route' => 'campaigns.index', 'icon' => 'fa-solid fa-bullhorn'],
            ],
            'Support' => [
                ['label' => 'Cases', 'route' => 'cases.index', 'icon' => 'fa-solid fa-life-ring'],
            ],
            'Admin' => $isAdminOrManager ? [
                ['label' => 'Products', 'route' => 'products.index', 'icon' => 'fa-solid fa-box-open'],
                ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'fa-solid fa-chart-line'],
                ['label' => 'Users', 'route' => 'users.index', 'icon' => 'fa-solid fa-users'],
                ['label' => 'Roles', 'route' => 'roles.index', 'icon' => 'fa-solid fa-user-shield'],
                ['label' => 'Teams', 'route' => 'teams.index', 'icon' => 'fa-solid fa-people-group'],
                ['label' => 'Files', 'route' => 'files.index', 'icon' => 'fa-solid fa-folder-open'],
                ['label' => 'Settings', 'route' => 'core.settings', 'icon' => 'fa-solid fa-gear'],
                ['label' => 'Audit Logs', 'route' => 'core.audit-logs', 'icon' => 'fa-solid fa-clipboard-list'],
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
                            'icon' => $item['icon'] ?? 'fa-solid fa-circle',
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

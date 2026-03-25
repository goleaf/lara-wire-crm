<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\Core\Livewire\Settings;
use Modules\Core\Models\Setting;
use Modules\Users\Models\Role;

function makeCoreSettingsRole(): Role
{
    return Role::query()->create([
        'name' => 'Core Settings Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => [
            'core' => true,
            'users' => true,
        ],
    ]);
}

test('authorized user can save full system settings payload', function () {
    $role = makeCoreSettingsRole();
    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('app_name', 'System Control Center')
        ->set('currency_symbol', '€')
        ->set('currency_code', 'eur')
        ->set('timezone', 'Europe/Vilnius')
        ->set('date_format', 'd/m/Y')
        ->set('pagination_size', 25)
        ->set('company_name', 'Acme UAB')
        ->set('company_address', 'Vilniaus g. 1, Vilnius')
        ->set('company_phone', '+37060000000')
        ->set('company_email', 'billing@acme.test')
        ->set('company_vat', 'LT123456789')
        ->set('bank_account_name', 'Acme UAB')
        ->set('bank_iban', 'LT121000011101001000')
        ->set('bank_swift', 'HABALT22')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('flash', type: 'success', message: 'Settings updated.');

    expect(Setting::getValue('crm.app_name'))->toBe('System Control Center');
    expect(Setting::getValue('crm.currency.symbol'))->toBe('€');
    expect(Setting::getValue('crm.currency.code'))->toBe('EUR');
    expect(Setting::getValue('crm.timezone'))->toBe('Europe/Vilnius');
    expect(Setting::getValue('crm.date_format'))->toBe('d/m/Y');
    expect(Setting::getValue('crm.pagination_size'))->toBe(25);
    expect(Setting::getValue('crm.company.name'))->toBe('Acme UAB');
    expect(Setting::getValue('crm.company.vat_number'))->toBe('LT123456789');
    expect(Setting::getValue('crm.company.bank_details.iban'))->toBe('LT121000011101001000');
});

test('authorized user can remove existing company logo', function () {
    Storage::fake('public');

    $role = makeCoreSettingsRole();
    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    Storage::disk('public')->put('crm/logo/current-logo.png', 'logo');
    Setting::setValue('crm.company.logo', 'crm/logo/current-logo.png');

    Livewire::actingAs($user)
        ->test(Settings::class)
        ->set('remove_logo', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('flash', type: 'success', message: 'Settings updated.');

    expect(Setting::getValue('crm.company.logo'))->toBe('');
    Storage::disk('public')->assertMissing('crm/logo/current-logo.png');
});

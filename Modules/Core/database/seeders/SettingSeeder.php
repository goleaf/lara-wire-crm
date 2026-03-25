<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'crm.app_name', 'value' => (string) config('crm.app_name', config('app.name')), 'type' => 'string'],
            ['key' => 'crm.currency.code', 'value' => (string) config('crm.default_currency.code', 'USD'), 'type' => 'string'],
            ['key' => 'crm.currency.symbol', 'value' => (string) config('crm.default_currency.symbol', '$'), 'type' => 'string'],
            ['key' => 'crm.timezone', 'value' => (string) config('crm.timezone', config('app.timezone', 'UTC')), 'type' => 'string'],
            ['key' => 'crm.date_format', 'value' => (string) config('crm.date_format', 'Y-m-d'), 'type' => 'string'],
            ['key' => 'crm.pagination_size', 'value' => (string) config('crm.pagination_size', 15), 'type' => 'integer'],
            ['key' => 'crm.company.name', 'value' => (string) data_get(config('crm.company'), 'name', ''), 'type' => 'string'],
            ['key' => 'crm.company.address', 'value' => (string) data_get(config('crm.company'), 'address', ''), 'type' => 'string'],
            ['key' => 'crm.company.phone', 'value' => (string) data_get(config('crm.company'), 'phone', ''), 'type' => 'string'],
            ['key' => 'crm.company.email', 'value' => (string) data_get(config('crm.company'), 'email', ''), 'type' => 'string'],
            ['key' => 'crm.company.vat', 'value' => (string) data_get(config('crm.company'), 'vat', ''), 'type' => 'string'],
            ['key' => 'crm.company.vat_number', 'value' => (string) data_get(config('crm.company'), 'vat_number', ''), 'type' => 'string'],
            ['key' => 'crm.company.logo', 'value' => (string) data_get(config('crm.company'), 'logo', ''), 'type' => 'string'],
            ['key' => 'crm.company.bank_details.account_name', 'value' => (string) data_get(config('crm.company'), 'bank_details.account_name', ''), 'type' => 'string'],
            ['key' => 'crm.company.bank_details.iban', 'value' => (string) data_get(config('crm.company'), 'bank_details.iban', ''), 'type' => 'string'],
            ['key' => 'crm.company.bank_details.swift', 'value' => (string) data_get(config('crm.company'), 'bank_details.swift', ''), 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }
    }
}

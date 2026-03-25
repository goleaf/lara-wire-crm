<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Activities\Database\Seeders\ActivitiesDatabaseSeeder;
use Modules\Calendar\Database\Seeders\CalendarDatabaseSeeder;
use Modules\Campaigns\Database\Seeders\CampaignsDatabaseSeeder;
use Modules\Cases\Database\Seeders\CasesDatabaseSeeder;
use Modules\Contacts\Database\Seeders\ContactsDatabaseSeeder;
use Modules\Deals\Database\Seeders\DealsDatabaseSeeder;
use Modules\Files\Database\Seeders\FilesDatabaseSeeder;
use Modules\Invoices\Database\Seeders\InvoicesDatabaseSeeder;
use Modules\Leads\Database\Seeders\LeadsDatabaseSeeder;
use Modules\Messaging\Database\Seeders\MessagingDatabaseSeeder;
use Modules\Notifications\Database\Seeders\NotificationsDatabaseSeeder;
use Modules\Products\Database\Seeders\ProductsDatabaseSeeder;
use Modules\Quotes\Database\Seeders\QuotesDatabaseSeeder;
use Modules\Reports\Database\Seeders\ReportsDatabaseSeeder;
use Modules\Users\Database\Seeders\UsersDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersDatabaseSeeder::class,
            FilesDatabaseSeeder::class,
            ProductsDatabaseSeeder::class,
            NotificationsDatabaseSeeder::class,
            ContactsDatabaseSeeder::class,
            LeadsDatabaseSeeder::class,
            DealsDatabaseSeeder::class,
            ActivitiesDatabaseSeeder::class,
            CalendarDatabaseSeeder::class,
            MessagingDatabaseSeeder::class,
            QuotesDatabaseSeeder::class,
            InvoicesDatabaseSeeder::class,
            CampaignsDatabaseSeeder::class,
            CasesDatabaseSeeder::class,
            ReportsDatabaseSeeder::class,
        ]);
    }
}

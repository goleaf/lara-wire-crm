<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Attributes\AsCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('crm:install {--fresh : Drop all tables and run migrate:fresh first} {--skip-migrate : Skip running migrations} {--skip-seed : Skip running seeders}')]
#[AsCommand(name: 'crm:install')]
#[Description('Run CRM migrations and seeders in the expected module order')]
class CrmInstallCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('Starting CRM installation...');

        if (! $this->option('skip-migrate')) {
            $this->line('Running database migrations...');

            $command = $this->option('fresh') ? 'migrate:fresh' : 'migrate';
            $exitCode = $this->call($command, ['--force' => true]);

            if ($exitCode !== self::SUCCESS) {
                $this->error('Migrations failed.');

                return self::FAILURE;
            }
        }

        if (! $this->option('skip-seed')) {
            $this->line('Seeding CRM data...');

            $exitCode = $this->call('db:seed', [
                '--class' => DatabaseSeeder::class,
                '--force' => true,
            ]);

            if ($exitCode !== self::SUCCESS) {
                $this->error('Seeding failed.');

                return self::FAILURE;
            }
        }

        $this->info('CRM installation completed.');

        return self::SUCCESS;
    }
}

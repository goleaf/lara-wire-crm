<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('activities') || Schema::hasColumn('activities', 'reminder_sent')) {
            return;
        }

        Schema::table('activities', function (Blueprint $table): void {
            $table->boolean('reminder_sent')
                ->default(false)
                ->after('reminder_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('activities') || ! Schema::hasColumn('activities', 'reminder_sent')) {
            return;
        }

        Schema::table('activities', function (Blueprint $table): void {
            $table->dropColumn('reminder_sent');
        });
    }
};

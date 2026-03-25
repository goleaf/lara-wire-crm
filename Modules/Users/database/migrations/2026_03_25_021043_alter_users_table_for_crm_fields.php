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
        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('role_id')->nullable()->after('password');
            $table->uuid('team_id')->nullable()->after('role_id');
            $table->boolean('is_active')->default(true)->after('team_id');
            $table->timestamp('last_login')->nullable()->after('is_active');
            $table->decimal('quota', 15, 2)->default(0)->after('last_login');
            $table->string('avatar_path')->nullable()->after('quota');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->nullOnDelete();

            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['team_id']);
            $table->dropColumn([
                'role_id',
                'team_id',
                'is_active',
                'last_login',
                'quota',
                'avatar_path',
            ]);
        });
    }
};

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
        Schema::create('dashboards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_public')->default(false);
            $table->json('layout')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'is_default'], 'dashboards_owner_default_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboards');
    }
};

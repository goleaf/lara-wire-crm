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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['Event', 'Cold Call', 'Direct Mail', 'In-person', 'Referral Program', 'Other'])->default('Other');
            $table->enum('status', ['Planned', 'Active', 'Completed', 'Paused'])->default('Planned');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->text('target_audience')->nullable();
            $table->unsignedInteger('expected_leads')->default(0);
            $table->text('description')->nullable();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['status', 'type'], 'campaigns_status_type_idx');
            $table->index(['owner_id', 'start_date'], 'campaigns_owner_start_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};

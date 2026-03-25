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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['Table', 'Bar', 'Line', 'Funnel', 'Pie', 'KPI', 'Area']);
            $table->enum('module', ['Deals', 'Contacts', 'Leads', 'Activities', 'Cases', 'Campaigns', 'Invoices', 'Quotes', 'Products']);
            $table->json('filters')->nullable();
            $table->string('group_by')->nullable();
            $table->json('metrics');
            $table->string('date_field')->nullable();
            $table->enum('date_range', ['Today', 'This Week', 'This Month', 'This Quarter', 'This Year', 'Last 30 Days', 'Last 90 Days', 'Custom'])->default('This Month');
            $table->date('custom_date_from')->nullable();
            $table->date('custom_date_to')->nullable();
            $table->boolean('is_scheduled')->default(false);
            $table->enum('schedule_frequency', ['Daily', 'Weekly', 'Monthly'])->nullable();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['module', 'type'], 'reports_module_type_idx');
            $table->index(['owner_id', 'is_public'], 'reports_owner_public_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

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
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dashboard_id')->constrained('dashboards')->cascadeOnDelete();
            $table->foreignUuid('report_id')->nullable()->constrained('reports')->nullOnDelete();
            $table->enum('widget_type', ['ReportChart', 'KPICard', 'ActivityFeed', 'PipelineFunnel', 'QuickStats', 'RecentDeals', 'OpenCases']);
            $table->string('title')->nullable();
            $table->unsignedInteger('position_x');
            $table->unsignedInteger('position_y');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->json('config')->nullable();
            $table->timestamps();

            $table->index(['dashboard_id', 'position_y', 'position_x'], 'dashboard_widgets_layout_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};

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
        Schema::create('cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'])->default('Open');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->enum('type', ['Bug', 'Feature Request', 'Question', 'Complaint', 'Other'])->default('Other');
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignUuid('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignUuid('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('sla_deadline')->nullable();
            $table->dateTime('first_response_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->unsignedTinyInteger('satisfaction_score')->nullable();
            $table->enum('channel', ['Phone', 'In-person', 'Internal Portal', 'Other'])->default('Other');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority'], 'cases_status_priority_idx');
            $table->index(['owner_id', 'sla_deadline'], 'cases_owner_sla_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};

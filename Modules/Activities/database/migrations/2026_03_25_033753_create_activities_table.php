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
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['Meeting', 'Task', 'Note', 'SMS']);
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('status', ['Planned', 'Completed', 'Cancelled'])->default('Planned');
            $table->enum('priority', ['Low', 'Normal', 'High'])->default('Normal');
            $table->dateTime('due_date')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->text('outcome')->nullable();
            $table->string('related_to_type')->nullable();
            $table->uuid('related_to_id')->nullable();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('reminder_at')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'due_date']);
            $table->index(['owner_id', 'status']);
            $table->index(['related_to_type', 'related_to_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

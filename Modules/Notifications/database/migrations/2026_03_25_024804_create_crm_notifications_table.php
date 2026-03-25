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
        Schema::create('crm_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->enum('type', ['Reminder', 'Mention', 'Assignment', 'SLA Breach', 'Deal Update', 'Task Due', 'Case Update', 'Payment Recorded', 'Quote Accepted', 'Other']);
            $table->string('title');
            $table->text('body')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('related_to_type')->nullable();
            $table->uuid('related_to_id')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'is_read', 'created_at'], 'crm_notifications_user_read_created_idx');
            $table->index(['related_to_type', 'related_to_id'], 'crm_notifications_related_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_notifications');
    }
};

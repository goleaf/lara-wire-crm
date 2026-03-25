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
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->string('extension', 20);
            $table->unsignedBigInteger('size_bytes');
            $table->string('disk')->default('local');
            $table->string('storage_path');
            $table->uuid('uploaded_by');
            $table->string('related_to_type')->nullable();
            $table->uuid('related_to_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->uuid('parent_file_id')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uploaded_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('parent_file_id')
                ->references('id')
                ->on('files')
                ->nullOnDelete();

            $table->index(['related_to_type', 'related_to_id']);
            $table->index(['mime_type', 'extension']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};

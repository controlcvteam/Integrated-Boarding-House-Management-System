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
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 50)->nullable()->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('category', 100)->default('General');
            $table->string('priority', 20)->default('medium');
            $table->string('status', 30)->default('pending');
            $table->date('preferred_date')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('assigned_to')->nullable();
            $table->date('target_date')->nullable();
            $table->text('remarks')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};

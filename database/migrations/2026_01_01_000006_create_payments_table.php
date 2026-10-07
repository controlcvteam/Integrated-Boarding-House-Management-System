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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code', 50)->nullable()->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->unsignedTinyInteger('billing_month'); // 1 - 12
            $table->unsignedSmallInteger('billing_year'); // e.g. 2026
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'gcash']);
            $table->date('payment_date');
            $table->string('gcash_reference', 100)->nullable();
            $table->string('receipt_path')->nullable();
            $table->enum('status', ['pending', 'paid', 'verified', 'rejected', 'partial'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('remarks')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

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
        // Add is_edited flag to payments table if not already present
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'is_edited')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->boolean('is_edited')->default(false)->after('status');
            });
        }

        // Create payment_edit_histories table
        if (!Schema::hasTable('payment_edit_histories')) {
            Schema::create('payment_edit_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('editor_name')->nullable();
                $table->text('reason');
                $table->json('changed_fields')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_edit_histories');

        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'is_edited')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('is_edited');
            });
        }
    }
};

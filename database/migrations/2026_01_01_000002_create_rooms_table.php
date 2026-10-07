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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number', 50)->unique();
            $table->string('room_name', 100)->nullable();
            $table->string('room_type', 100); // Single, Double, Deluxe, Family, etc.
            $table->unsignedInteger('capacity')->default(1);
            $table->decimal('monthly_rent', 10, 2);
            $table->string('floor', 50)->nullable(); // 1st Floor, 2nd Floor, etc.
            $table->text('description')->nullable();
            $table->json('amenities')->nullable(); // Array of amenities
            $table->boolean('manual_available')->default(true); // Landlady manual toggle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

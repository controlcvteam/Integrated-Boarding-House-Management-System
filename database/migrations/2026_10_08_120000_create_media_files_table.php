<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('path', 255)->unique();
            $table->string('mime_type', 100)->default('image/jpeg');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->longText('data'); // Base64 encoded binary data
            $table->timestamps();

            $table->index('path');
        });

        // Clean up legacy '0' values in users, tenants, and room_images
        try {
            DB::table('users')->where('profile_picture', '0')->update(['profile_picture' => null]);
            DB::table('tenants')->where('profile_picture', '0')->update(['profile_picture' => null]);
            DB::table('room_images')->where('image_path', '0')->delete();
        } catch (\Throwable $e) {
            // Ignore if tables don't have records
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};

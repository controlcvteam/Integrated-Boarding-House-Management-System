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
        if (!Schema::hasColumn('users', 'profile_picture')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_picture')->nullable()->after('gender');
            });
        }

        if (!Schema::hasColumn('tenants', 'profile_picture')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('profile_picture')->nullable()->after('full_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'profile_picture')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('profile_picture');
            });
        }

        if (Schema::hasColumn('tenants', 'profile_picture')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('profile_picture');
            });
        }
    }
};

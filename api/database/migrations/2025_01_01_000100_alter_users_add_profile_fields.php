<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->nullable()->after('name');
            $table->string('phone')->nullable()->after('city');
            $table->string('region')->nullable()->index()->after('phone');
            $table->string('language', 10)->nullable()->after('region');
            $table->string('profile_image_path')->nullable()->after('language');
            // Invitations
            $table->string('invite_token', 64)->nullable()->unique()->after('remember_token');
            $table->timestamp('invited_at')->nullable()->after('invite_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'city', 'phone', 'region', 'language', 'profile_image_path', 'invite_token', 'invited_at'
            ]);
        });
    }
};


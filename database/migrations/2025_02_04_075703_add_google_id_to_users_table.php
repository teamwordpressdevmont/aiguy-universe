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
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_verified')->nullable()->after('email_verified_at');
            $table->string('avatar')->nullable()->after('email_verified');
            $table->string('google_id')->nullable()->unique()->after('avatar');
            $table->string('google_token')->nullable()->after('google_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verified', 'avatar', 'google_id', 'google_token']);
        });
    }
};

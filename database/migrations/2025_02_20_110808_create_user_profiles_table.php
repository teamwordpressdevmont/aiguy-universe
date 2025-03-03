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
        Schema::create('user_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete( 'cascade' );
            $table->string( 'first_name' );
            $table->string( 'last_name' )->nullable();
            $table->string( 'phone_num' )->nullable();
            $table->string( 'industry' )->nullable();
            $table->string( 'ai_expertise_level' )->nullable();
            $table->string( 'area_of_interest' )->nullable();
            $table->integer( 'view_platform' )->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile');
    }
};

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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete( 'cascade' );
            $table->string('name');
            $table->string( 'slug' )->unique();
            $table->string('featured_image')->nullable(); // Image URL or Path
            $table->string('reading_time')->nullable(); 
            $table->longText('content')->nullable(); // blog content
            $table->string('left_image')->nullable();
            $table->text('right_text')->nullable();
            $table->text('middle_text')->nullable();
            $table->string('middle_image')->nullable();
            $table->string('sub_title')->nullable();
            $table->text('sub_content')->nullable();
            $table->string('sub_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};

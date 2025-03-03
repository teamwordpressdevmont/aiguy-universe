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
        Schema::create('community_like_dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete( 'cascade' );
            $table->foreignId('category_id')->constrained('ai_tool_category')->onDelete( 'cascade' );
            $table->foreignId('question_id')->constrained('community_questions')->onDelete( 'cascade' );
            $table->foreignId('community_answer_id')->constrained('community_answers')->onDelete( 'cascade' );
            $table->integer( 'like' )->default(0);
            $table->integer( 'dislike' )->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_like_dislikes');
    }
};

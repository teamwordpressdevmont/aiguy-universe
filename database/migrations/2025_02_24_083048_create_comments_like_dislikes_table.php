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
        Schema::create('comments_like_dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete( 'cascade' );
            $table->foreignId('tool_id')->constrained('ai_tools')->onDelete( 'cascade' );
            $table->foreignId('comment_id')->constrained('comment_questions')->onDelete( 'cascade' );
            $table->foreignId('comment_answer_id')->constrained('comment_answers')->onDelete( 'cascade' );
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
        Schema::dropIfExists('comments_like_dislikes');
    }
};

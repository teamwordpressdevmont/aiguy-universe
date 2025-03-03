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
        Schema::create('community_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete( 'cascade' );
            $table->foreignId('category_id')->constrained('ai_tool_category')->onDelete( 'cascade' );
            $table->foreignId('community_question_id')->constrained('community_questions')->onDelete( 'cascade' );
            $table->integer( 'parent_answer_id' );
            $table->longtext( 'answer' );
            $table->integer('approved')->default(1);
            $table->integer( 'like_count' )->default(0);
            $table->integer( 'dislike_count' )->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_answers');
    }
};

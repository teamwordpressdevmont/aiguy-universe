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
        Schema::create('comment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete( 'cascade' );
            $table->foreignId('tool_id')->constrained('ai_tools')->onDelete( 'cascade' );
            $table->foreignId('comment_id')->constrained('comment_questions')->onDelete( 'cascade' );
            $table->string( 'comment' );
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
        Schema::dropIfExists('comment_answers');
    }
};

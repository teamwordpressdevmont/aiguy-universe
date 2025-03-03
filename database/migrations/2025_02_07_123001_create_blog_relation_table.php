<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('blog_relation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('blog_category')->onDelete('cascade');
            $table->timestamps();

            
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_relation');
    }
};
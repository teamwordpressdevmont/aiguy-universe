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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); 
            $table->string('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->enum('pricing', ['Free', 'Paid'])->default('Free');
            $table->longText('affiliate_link')->nullable();
            $table->integer('members')->nullable();
            $table->string('courses_filter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

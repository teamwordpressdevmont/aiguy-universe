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
        Schema::create('ai_tools', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable(); 
            $table->string('cover')->nullable();
            $table->string('tagline')->nullable();
            $table->string('short_description_heading')->nullable(); 
            $table->text('short_description')->nullable(); 
            $table->string( 'release_date' )->nullable();
            $table->boolean('verified_status')->nullable(); 
            $table->integer('payment_status')->nullable();
            $table->string('payment_text')->nullable();
            $table->string( 'platform_compatibility' )->nullable();
            $table->string('website_link')->nullable();
            $table->string('description_heading')->nullable(); 
            $table->longText('description')->nullable();
            $table->longText('key_features')->nullable(); 
            $table->longText('pros')->nullable(); 
            $table->longText('cons')->nullable();
            $table->longText('voila_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->string('aitool_filter')->nullable();
            $table->integer('added_by')->nullable();
            $table->float('avg_rating', 3, 2)->nullable();
            $table->integer('reviews_received')->nullable();
            $table->integer('integration_capabilities')->default(0);
            $table->timestamps();
        });
    }
 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tools');
    }
};

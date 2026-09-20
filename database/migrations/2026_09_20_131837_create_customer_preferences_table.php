<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('favorite_categories')->nullable();
            $table->json('favorite_food_types')->nullable();
            $table->json('favorite_beverages')->nullable();
            $table->string('preferred_taste')->nullable(); // sweet, salty, sour...
            $table->json('dietary_preferences')->nullable(); // vegetarian, low-carb...
            $table->enum('price_preference', ['low', 'medium', 'high'])->nullable();
            $table->unsignedTinyInteger('spicy_level')->default(0);
            $table->json('favorite_ingredients')->nullable();
            $table->json('disliked_ingredients')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_preferences');
    }
};

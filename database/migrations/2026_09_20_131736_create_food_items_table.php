<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->json('ingredients')->nullable();
            $table->unsignedInteger('calories')->nullable();
            $table->unsignedTinyInteger('spicy_level')->default(0); // 0 - 5
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedSmallInteger('preparation_time')->nullable(); // بالدقايق
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};

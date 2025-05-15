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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->unsigned()->default(5); // Od 1 do 5
            $table->text('content');
            $table->string('title')->nullable();
            $table->boolean('is_approved')->default(false); // Recenzje domyślnie trafiają do moderacji
            $table->boolean('is_featured')->default(false); // Czy ma być wyświetlana na stronie głównej
            $table->timestamps();
            
            $table->unique(['user_id', 'product_id']); // Jeden użytkownik może dodać tylko jedną recenzję na produkt
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

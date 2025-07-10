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
        Schema::table('orders', function (Blueprint $table) {
            // Usuwamy niepotrzebne pola
            $table->dropColumn([
                'session_id',          // Nie używamy sesji płatności
                'promotion_code',      // Używamy discount zamiast tego
                'payment_intent_id',   // Nie używamy Stripe payment intents
                'country'             // Usuwamy pole country zgodnie z prośbą
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Przywracamy usunięte pola
            $table->string('session_id')->nullable();
            $table->string('promotion_code')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->string('country');
        });
    }
}; 
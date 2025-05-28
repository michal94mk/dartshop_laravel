<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tworzymy tabelę promotions z nowym systemem
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('name'); // zachowujemy dla kompatybilności
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_order_value', 10, 2)->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->string('badge_text')->nullable();
            $table->string('badge_color')->default('#ff0000');
            $table->timestamps();
        });

        // Tworzymy tabelę pivot dla produktów i promocji
        Schema::create('product_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['product_id', 'promotion_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_promotions');
        Schema::dropIfExists('promotions');
    }
}; 
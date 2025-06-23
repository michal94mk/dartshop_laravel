<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Sprawdzamy czy tabela promotions już istnieje, jeśli nie - tworzymy ją
        if (!Schema::hasTable('promotions')) {
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
        } else {
            // Jeśli tabela istnieje, dodajemy brakujące kolumny
            Schema::table('promotions', function (Blueprint $table) {
                if (!Schema::hasColumn('promotions', 'title')) {
                    $table->string('title')->after('id');
                }
                if (!Schema::hasColumn('promotions', 'display_order')) {
                    $table->integer('display_order')->default(0);
                }
                if (!Schema::hasColumn('promotions', 'is_featured')) {
                    $table->boolean('is_featured')->default(false);
                }
                if (!Schema::hasColumn('promotions', 'badge_text')) {
                    $table->string('badge_text')->nullable();
                }
                if (!Schema::hasColumn('promotions', 'badge_color')) {
                    $table->string('badge_color')->default('#ff0000');
                }
            });
        }

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
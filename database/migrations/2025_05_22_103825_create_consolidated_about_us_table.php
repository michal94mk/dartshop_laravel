<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usuwamy stare tabele about_page_images i about_pages, jeśli istnieją
        Schema::dropIfExists('about_page_images');
        Schema::dropIfExists('about_pages');
        
        // Usuwamy tabelę about_us, jeśli istnieje
        Schema::dropIfExists('about_us');
        
        // Tworzymy nową tabelę about_us z wszystkimi potrzebnymi kolumnami
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->string('image_path')->nullable();
            $table->string('image_position')->default('right');
            $table->string('header_style')->nullable()->default('text-3xl font-bold text-gray-900');
            $table->string('header_margin')->default('mb-6');
            $table->string('content_layout')->default('prose-lg');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
        
        // Przywracamy oryginalne tabele z podstawową strukturą
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });
        
        Schema::create('about_page_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_page_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }
};

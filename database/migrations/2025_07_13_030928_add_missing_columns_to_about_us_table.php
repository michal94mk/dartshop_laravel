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
        Schema::table('about_us', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('content');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('image_path')->nullable()->after('meta_description');
            $table->string('image_position')->nullable()->after('image_path');
            $table->string('header_style')->nullable()->after('image_position');
            $table->string('header_margin')->nullable()->after('header_style');
            $table->string('content_layout')->nullable()->after('header_margin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'image_path',
                'image_position',
                'header_style',
                'header_margin',
                'content_layout'
            ]);
        });
    }
};

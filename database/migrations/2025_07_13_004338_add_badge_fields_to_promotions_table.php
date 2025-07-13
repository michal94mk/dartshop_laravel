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
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('badge_text')->nullable()->after('description');
            $table->string('badge_color', 7)->default('#ff0000')->after('badge_text');
            $table->boolean('is_featured')->default(false)->after('badge_color');
            $table->integer('display_order')->default(0)->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['badge_text', 'badge_color', 'is_featured', 'display_order']);
        });
    }
};

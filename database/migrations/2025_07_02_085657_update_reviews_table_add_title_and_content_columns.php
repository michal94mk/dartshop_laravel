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
        Schema::table('reviews', function (Blueprint $table) {
            // Add new columns
            $table->string('title')->nullable()->after('rating');
            $table->text('content')->nullable()->after('title');
            $table->boolean('is_featured')->default(false)->after('is_approved');
        });
        
        // Migrate existing data from comment to content and add default titles
        DB::statement("UPDATE reviews SET content = comment WHERE comment IS NOT NULL");
        DB::statement("UPDATE reviews SET title = 'Recenzja produktu' WHERE title IS NULL");
        
        // Now make title and content non-nullable
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->text('content')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'is_featured']);
        });
    }
};

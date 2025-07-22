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
        Schema::table('products', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            
            // Make columns nullable
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('brand_id')->nullable()->change();
            
            // Add new foreign key constraints with SET NULL on delete
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
                  
            $table->foreign('brand_id')
                  ->references('id')
                  ->on('brands')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the new foreign key constraints
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            
            // Make columns not nullable again
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->unsignedBigInteger('brand_id')->nullable(false)->change();
            
            // Restore original foreign key constraints with CASCADE
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
                  
            $table->foreign('brand_id')
                  ->references('id')
                  ->on('brands')
                  ->onDelete('cascade');
        });
    }
};

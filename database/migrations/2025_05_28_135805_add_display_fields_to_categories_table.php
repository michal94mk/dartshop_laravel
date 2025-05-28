<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('image')->nullable()->after('description');
            $table->string('slug')->nullable()->after('image');
            $table->integer('sort_order')->default(0)->after('slug');
        });

        // Generate slugs for existing categories
        $categories = DB::table('categories')->whereNull('slug')->get();
        
        foreach ($categories as $category) {
            $slug = Str::slug($category->name);
            $originalSlug = $slug;
            $counter = 1;

            // Make sure slug is unique
            while (DB::table('categories')->where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            DB::table('categories')
                ->where('id', $category->id)
                ->update(['slug' => $slug]);
        }

        // Now make slug unique
        Schema::table('categories', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['description', 'image', 'slug', 'sort_order']);
        });
    }
};

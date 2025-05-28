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
        // First, handle any empty or null slug values
        $categories = DB::table('categories')->where('slug', '')->orWhereNull('slug')->get();
        
        foreach ($categories as $category) {
            $slug = Str::slug($category->name);
            if (empty($slug)) {
                $slug = 'category-' . $category->id;
            }
            
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

        // Now add the unique constraint if it doesn't exist
        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->unique('slug');
            });
        } catch (\Exception $e) {
            // Constraint may already exist, ignore the error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });
    }
};

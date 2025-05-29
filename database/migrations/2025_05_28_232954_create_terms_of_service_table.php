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
        Schema::create('terms_of_service', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('version', 20)->default('1.0');
            $table->boolean('is_active')->default(false);
            $table->timestamp('effective_date');
            $table->timestamps();
            
            $table->index(['is_active', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_of_service');
    }
};

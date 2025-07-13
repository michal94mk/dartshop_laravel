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
        Schema::table('terms_of_service', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->string('version', 20)->nullable()->after('title');
            $table->date('effective_date')->nullable()->after('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_of_service', function (Blueprint $table) {
            $table->dropColumn(['title', 'version', 'effective_date']);
        });
    }
};

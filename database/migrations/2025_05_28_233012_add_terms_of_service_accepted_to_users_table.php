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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('terms_of_service_accepted')->default(false)->after('privacy_policy_accepted_at');
            $table->timestamp('terms_of_service_accepted_at')->nullable()->after('terms_of_service_accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['terms_of_service_accepted', 'terms_of_service_accepted_at']);
        });
    }
};

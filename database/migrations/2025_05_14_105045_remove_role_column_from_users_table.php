<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Run the RolesAndPermissionsSeeder first to ensure all users have proper roles
        $seeder = new \Database\Seeders\RolesAndPermissionsSeeder();
        $seeder->run();
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
        });
        
        // Restore roles from Spatie roles to the role column
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if ($user->hasRole('admin')) {
                $user->role = 'admin';
                $user->save();
            } else {
                $user->role = 'user';
                $user->save();
            }
        }
    }
};

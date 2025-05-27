<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:admin-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check admin users data for first_name and last_name fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admins = User::where('is_admin', true)->get();
        
        $this->info('Admin users in database:');
        $this->line('');
        
        foreach ($admins as $admin) {
            $this->line("ID: {$admin->id}");
            $this->line("Name: {$admin->name}");
            $this->line("First Name: " . ($admin->first_name ?: 'NULL/EMPTY'));
            $this->line("Last Name: " . ($admin->last_name ?: 'NULL/EMPTY'));
            $this->line("Email: {$admin->email}");
            $this->line("Is Admin: " . ($admin->is_admin ? 'YES' : 'NO'));
            $this->line('---');
        }
        
        $this->info("Total admin users: " . $admins->count());
        
        return 0;
    }
}

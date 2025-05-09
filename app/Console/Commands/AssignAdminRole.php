<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-admin-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign the admin role to all existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        
        if (!$adminRole) {
            $this->error('Admin role not found!');
            return 1;
        }
        
        $users = User::whereDoesntHave('roles', function ($query) use ($adminRole) {
            $query->where('role_id', $adminRole->id);
        })->get();
        
        $count = 0;
        foreach ($users as $user) {
            $user->roles()->syncWithoutDetaching([$adminRole->id]);
            $count++;
        }
        
        $this->info("Admin role assigned to {$count} users!");
        
        return 0;
    }
} 
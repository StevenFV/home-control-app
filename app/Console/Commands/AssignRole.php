<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-role {user} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $roleName = $this->argument('role');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID $userId not found.");
            return;
        }

        $role = Role::findByName($roleName);
        if (!$role) {
            $this->error("Role '$roleName' not found.");
            return;
        }

        $user->assignRole($role);

        $this->info("Role '$roleName' assigned to user $userId.");
    }
}

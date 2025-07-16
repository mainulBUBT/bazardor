<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CompleteRolePermissionSeeder;

class SetupRolesAndPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup roles and permissions for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up roles and permissions...');
        
        $seeder = new CompleteRolePermissionSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('Roles and permissions setup completed successfully!');
        
        return 0;
    }
}
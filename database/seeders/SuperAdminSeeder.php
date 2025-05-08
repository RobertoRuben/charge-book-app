<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use BezhanSalleh\FilamentShield\Support\Utils;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRoleName = Utils::getSuperAdminName();
        
        $superAdminRole = Role::firstOrCreate(['name' => $superAdminRoleName]);
        
        $allPermissions = Permission::all();
        
        $superAdminRole->syncPermissions($allPermissions);
        
        $superAdmin = User::find(1);
        
        if ($superAdmin) {
            $superAdmin->assignRole($superAdminRole);
            
            $this->command->info('¡Usuario con ID 1 ha sido configurado como Super Admin con todos los permisos!');
        } else {
            $this->command->error('No se encontró ningún usuario con ID 1. Por favor, asegúrate de que existe.');
        }
    }
}

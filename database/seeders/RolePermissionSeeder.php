<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       

        $adminRole = Role::create(['name' => 'admin']);
        $adminPermission = Permission::create(['name' => 'view admin']);
        
        $adminRole->givePermissionTo($adminPermission);
        
        $userRole = Role::create(['name' => 'user']);
        $userPermission = Permission::create(['name' => 'view user']);
        
        $userRole->givePermissionTo($userPermission);
        

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@aiguy.com',
            'password' => Hash::make('password'),
        ]);
        
        $admin->assignRole('admin');
        
        $user = User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
        ]);
        
        $user->assignRole('user');
    }
}
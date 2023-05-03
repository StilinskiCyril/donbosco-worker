<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // roles
        $roles = ['user', 'treasurer', 'admin', 'super-admin'];

        foreach ($roles as $role) {
            $created_role = Role::firstOrCreate(['name' => $role]);
            if($created_role->name == 'user'){
                $created_role->givePermissionTo([

                ]);
            } else if($created_role->name == 'treasurer') {
                $created_role->givePermissionTo([

                ]);
            } else if($created_role->name == 'admin') {
                $created_role->givePermissionTo([

                ]);
            } else if($created_role->name == 'super-admin') {
                $created_role->givePermissionTo([

                ]);
            }
        }
    }
}

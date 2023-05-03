<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // permissions
        $permissions = [
            'create-user',
            'read-user',
            'update-user',
            'delete-user',

            'create-projects',
            'read-projects',
            'update-projects',
            'delete-projects',

            'create-accounts',
            'read-accounts',
            'update-accounts',
            'delete-accounts',

            'create-treasurers',
            'read-treasurers',
            'update-treasurers',
            'delete-treasurers',

            'create-expenses',
            'read-expenses',
            'update-expenses',
            'delete-expenses',

            'create-groups',
            'read-groups',
            'update-groups',
            'delete-groups',

            'create-sub_groups',
            'read-sub_groups',
            'update-sub_groups',
            'delete-sub_groups',

            'create-donors',
            'read-donors',
            'update-donors',
            'delete-donors',

            'create-pledges',
            'read-pledges',
            'update-pledges',
            'delete-pledges',

            'manage-roles',
            'manage-permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}

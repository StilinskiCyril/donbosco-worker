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

            'create-mpesa-access-tokens',
            'read-mpesa-access-tokens',
            'update-mpesa-access-tokens',
            'delete-mpesa-access-tokens',

            'create-otps',
            'read-otps',
            'update-otps',
            'delete-otps',

            'create-donations',
            'read-donations',
            'update-donations',
            'delete-donations',

            'create-pending-mpesa-donations',
            'read-pending-mpesa-donations',
            'update-pending-mpesa-donations',
            'delete-pending-mpesa-donations',

            'create-unknown-donations',
            'read-unknown-donations',
            'update-unknown-donations',
            'delete-unknown-donations',

            'manage-roles',
            'manage-permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}

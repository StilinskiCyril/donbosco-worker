<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_admin = User::where('msisdn', '254705799644')->first();
        if(!$super_admin){
            $user_super_admin = User::create([
                'name' => 'Cyril Super Admin',
                'msisdn' => '254705799644',
                'msisdn_verified_at' => now(),
                'email' => 'aguvasucyrilsuperadmin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('aguvasucyrilsuperadmin@gmail.com'),
                'remember_token' => Str::random(10),
            ]);
            $user_super_admin->assignRole('super-admin');
        }

        $admin = User::where('msisdn', '254705799645')->first();
        if(!$admin){
            $user_admin = User::create([
                'name' => 'Cyril Admin',
                'msisdn' => '254705799645',
                'msisdn_verified_at' => now(),
                'email' => 'aguvasucyriladmin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('aguvasucyriladmin@gmail.com'),
                'remember_token' => Str::random(10),
            ]);
            $user_admin->assignRole('admin');
        }

//        $treasurer = User::where('msisdn', '254705799646')->first();
//        if(!$treasurer){
//            $user_treasurer = User::create([
//                'name' => 'Cyril Admin',
//                'msisdn' => '254705799646',
//                'msisdn_verified_at' => now(),
//                'email' => 'aguvasucyriltreasurer@gmail.com',
//                'email_verified_at' => now(),
//                'password' => Hash::make('aguvasucyriltreasurer@gmail.com'),
//                'remember_token' => Str::random(10),
//            ]);
//            $user_treasurer->assignRole('treasurer');
//        }

        $user = User::where('msisdn', '254705799647')->first();
        if(!$user){
            $user_user = User::create([
                'name' => 'Cyril Admin',
                'msisdn' => '254705799647',
                'msisdn_verified_at' => now(),
                'email' => 'aguvasucyriluser@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('aguvasucyriluser@gmail.com'),
                'remember_token' => Str::random(10),
            ]);
            $user_user->assignRole('user');
        }
    }
}

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
        $admin = User::where('msisdn', '254705799644')->first();
        if(!$admin){
            $admin = User::create([
                'name' => 'Cyril Aguvasu',
                'msisdn' => '254705799644',
                'msisdn_verified_at' => now(),
                'email' => 'aguvasucyril@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('aguvasucyril@gmail.com'),
                'remember_token' => Str::random(10),
            ]);
            $admin->assignRole('admin');
        }
    }
}

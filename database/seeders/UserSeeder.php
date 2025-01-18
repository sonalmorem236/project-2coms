<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Jack',
                'email' => 'jack123@gmail.com',
                'password' => Hash::make(123456),
            ],
            [
                'name' => 'John',
                'email' => 'john@gmail.com',
                'password' => Hash::make(123456),
            ],
        ];

        foreach($users as $user){
            $userCheck = User::where(['email' => $user['email']])->first();
            if(!$userCheck){
                User::create($user);
            }
        }
    }
}

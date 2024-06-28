<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user  = new User();
        $user->name = 'Admin1';
        $user->email = 'abc1@gmail.com';
        $user->password = Hash::make('12341');
        $user->save();
    }
}

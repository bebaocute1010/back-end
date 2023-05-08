<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => 0,
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => '123123',
            'role' => 0,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Alan Pratama',
            // 'slug' => 'alan-pratama',
            'email' => 'pralan76@gmail.com',
            'phone' => '085817000942',

            'username' => 'lalan',
            'password' => bcrypt('lalan'),
        ]);
    }
}

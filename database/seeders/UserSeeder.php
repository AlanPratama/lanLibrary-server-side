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
            'role' => 'user',
            'position' => 'Member',

            'name' => 'Zikriyandri Riedwan',
            'slug' => 'zikriyandri-riedwan',
            'email' => 'zikriyandri@gmail.com',
            'phone' => '0858170002311',

            'username' => 'zikriri',
            'password' => bcrypt('zikriri'),
        ]);

        User::create([
            'role' => 'user',
            'position' => 'Member',

            'name' => 'Fathul Bari',
            'slug' => 'fathul-bari',
            'email' => 'fbariaja@gmail.com',
            'phone' => '0858170273930',

            'username' => 'fbariaja',
            'password' => bcrypt('fbariaja'),
        ]);

        User::create([
            'role' => 'user',
            'position' => 'Member',

            'name' => 'Helmi Fawwaz Raihan',
            'slug' => 'helmi-fawwaz-raihan',
            'email' => 'helmi@gmail.com',
            'phone' => '0858170203330',

            'username' => 'helmiF',
            'password' => bcrypt('helmiF'),
        ]);

        User::create([
            'role' => 'user',
            'position' => 'Member',

            'name' => 'Muhammad Ridho',
            'slug' => 'muhammad-ridho',
            'email' => 'ridho@gmail.com',
            'phone' => '082420273930',

            'username' => 'ridhoR',
            'password' => bcrypt('ridhoR'),
        ]);

        User::create([
            'role' => 'user',
            'position' => 'Member',

            'name' => 'Arva Revanza Eferio Wempysono',
            'slug' => 'arva-revanza-eferio-wempysono',
            'email' => 'arva@gmail.com',
            'phone' => '08893870273930',

            'username' => 'arva',
            'password' => bcrypt('arva'),
        ]);
    }
}

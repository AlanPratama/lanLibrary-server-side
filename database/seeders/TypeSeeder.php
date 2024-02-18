<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Type::create([
            'name' => 'Umum',
        ]);

        Type::create([
            'name' => 'Novel',
        ]);

        Type::create([
            'name' => 'Manga',
        ]);

    }
}

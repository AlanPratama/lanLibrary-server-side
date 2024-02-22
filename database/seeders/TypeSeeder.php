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
            'slug' => 'umum',
        ]);

        Type::create([
            'name' => 'Novel',
            'slug' => 'novel',
        ]);

        Type::create([
            'name' => 'Manga',
            'slug' => 'manga',
        ]);

    }
}

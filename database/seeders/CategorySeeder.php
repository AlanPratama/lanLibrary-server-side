<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Ensiklopedia',
            'slug' => 'Ensiklopedia'
        ]);


        Category::create([
            'name' => 'Pengetahuan Umum',
            'slug' => Str::slug('Pengetahuan Umum')
        ]);

        Category::create([
            'name' => 'Comedy',
            'slug' => Str::slug('comedy'),
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Writer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WriterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $writers = [
            [
                'name' => 'Nananana Nanana',
            ],
            [
                'name' => 'Babababa Babababa',
            ],
            [
                'name' => 'Lalalala Lalalala',
            ],
        ];


        foreach ($writers as $writer) {
            Writer::create([
                'name' => $writer['name'],
                'slug' => Str::slug($writer['name']),
            ]);
        }
    }
}

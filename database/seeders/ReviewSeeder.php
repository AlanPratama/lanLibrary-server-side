<?php

namespace Database\Seeders;

use App\Models\Reviews;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reviews::create([
            'code' => 'lfnakcpsje2048',
            'user_id' => 1,
            'book_id' => 1,
            'score' => 5,
            'comment' => 'Walawee, ini buku sangat bagus looo... rekomen sangat buat lo olang baca!'
        ]);

        Reviews::create([
            'code' => 'akspeojficjs23',
            'user_id' => 2,
            'book_id' => 1,
            'score' => 5,
            'comment' => 'Walawee, ini buku sangat bagus looo... rekomen sangat buat lo olang baca!'
        ]);
    }
}

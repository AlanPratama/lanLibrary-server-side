<?php

namespace Database\Seeders;

use App\Models\Rentlogs;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rentlogs::create([
            'code' => Str::random(5),
            'user_id' => 1,
            'book_id' => 1,
            'date_start' => Carbon::now()->toDateString(),
            'date_finish' => Carbon::now()->addDays(5)->toDateString(),
            'return' => null,
            'day_late' => null,
            'penalties' => null,
        ]);
    }
}

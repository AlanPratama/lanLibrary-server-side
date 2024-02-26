<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_id' => 1,
            'total_book' => 20,
            'title' => fake()->title(),
            'slug' => Str::slug(fake()->title()),
            'writer_id' => 1,
            'publisher' => fake()->name(),
            'description' => fake()->text(),
            'year' => 2024,
            'page' => 50,
            'cover' => '/assets/404-book-img.png'
        ];
    }
}

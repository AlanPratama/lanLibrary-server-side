<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Book extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];


    public function rentlogs() {
        return $this->hasMany(Rentlogs::class, 'book_id');
    }


    // SIDE DISH FOR BOOK

    public function types() {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'pivot_categories', 'book_id', 'category_id');
    }






    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

}

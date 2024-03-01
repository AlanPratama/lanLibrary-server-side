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

    public function writers() {
        return $this->belongsTo(Writer::class, 'writer_id');
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'pivot_categories', 'book_id', 'category_id');
    }

    // ADD TO FAVORITE FOR BOOK
    public function favotites() {
        return $this->hasMany(Favorite::class, 'book_id');
    }

    // REVIEWS RELATIONAL
    public function reviews() {
        return $this->hasMany(Reviews::class, 'user_id');
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

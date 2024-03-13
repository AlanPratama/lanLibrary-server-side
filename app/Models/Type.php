<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Type extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];

    public function books() {
        return $this->hasMany(Book::class, 'type_id');
    }



    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}

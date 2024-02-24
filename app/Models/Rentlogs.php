<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rentlogs extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function users() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function books() {
        return $this->belongsTo(Book::class, 'book_id');
    }


    public function reviews() {
        return $this->belongsTo(Reviews::class, 'reviews');
    }

}

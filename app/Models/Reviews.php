<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function users() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function books() {
        return $this->belongsTo(Book::class, 'book_id');
    }


    public function rentlogs() {
        return $this->hasMany(Rentlogs::class, 'reviews');
    }



}

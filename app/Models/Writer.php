<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Writer extends Model
{
    use HasFactory;

    protected $table = 'writer';
    protected $guarded = ['id'];

    public function books() {
        return $this->hasMany(Book::class, 'writer_id');
    }

}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Cviebrock\EloquentSluggable\Sluggable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Sluggable;



    public function rentlogs() {
        return $this->hasMany(Rentlogs::class, 'user_id');
    }

    public function presents() {
        return $this->hasMany(Present::class, 'user_id');
    }

    public function favotites() {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function reviews() {
        return $this->hasMany(Reviews::class, 'user_id');
    }


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'username'
            ]
        ];
    }




    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}

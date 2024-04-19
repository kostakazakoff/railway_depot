<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    
    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }


    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }


    public function stores(): BelongsToMany
    {
        return $this
        ->BelongsToMany(Store::class)
        ->withTimestamps()
        ->as('responsibilities'); //TODO: ?
    }
}

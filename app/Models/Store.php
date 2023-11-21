<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name'
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'inventories');
    }
}

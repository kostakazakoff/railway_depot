<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name'
    ];

    public function articles(): belongsToMany
    {
        return $this->belongsToMany(Article::class, 'inventories');
    }

    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->withTimestamps()
            ->as('responsibles');
    }
}

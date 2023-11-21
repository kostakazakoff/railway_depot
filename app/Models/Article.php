<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'inventory_number',
        'catalog_number',
        'draft_number',
        'material_number',
        'description',
        'price',
    ];

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'inventories');
    }
}

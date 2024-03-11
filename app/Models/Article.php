<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Concerns\Filterable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    use HasFactory, Filterable;


    protected $fillable = [
        'inventory_number',
        'catalog_number',
        'draft_number',
        'material',
        'description',
        'price',
    ];


    public function stores()
    {
        return $this->belongsToMany(Store::class, 'inventories');
    }


    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'article_id', 'id');
    }

    
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class, 'article_id', 'id');
    }
}

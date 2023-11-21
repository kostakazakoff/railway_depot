<?php

namespace App\Models;

use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;


/* Images table OneToMany */
class Article extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'inventory_number',
        'catalog_number',
        'draft_number',
        'material', // Materials?
        'description',
        'price',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('inventory_number')
            ->saveSlugsTo('slug');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'inventories');
    }
}

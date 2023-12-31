<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Concerns\Filterable;


class Article extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Prunable, Filterable;


    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    protected $fillable = [
        'inventory_number',
        'catalog_number',
        'draft_number',
        'material',
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


    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'article_id', 'id');
    }


    public function prunable(): Builder
    {
        return static::where('deleted_at', '!=', null);
    }
}

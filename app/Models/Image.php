<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;


class Image extends Model
{
    use HasFactory, SoftDeletes, Prunable;

    protected $hidden = ['created_on', 'updated_on'];

    protected $fillable = ['filename', 'path', 'article_id'];

    
    public function articles(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }


    public function prunable(): Builder
    {
        return static::where('deleted_at', '!=', null);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = ['created_on', 'updated_on'];

    protected $fillable = ['filename', 'path', 'article_id'];

    
    public function articles(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}

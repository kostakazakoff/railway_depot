<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $hidden = ['created_on', 'updated_on'];

    protected $fillable = ['filename', 'path'];

    public function articles()
    {
        return $this->belongsTo(Article::class);
    }
}

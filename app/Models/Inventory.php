<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'article_id',
        'store_id',
        'quantity',
        'package',
        'position'
    ];
}

// TODO: HasOne article

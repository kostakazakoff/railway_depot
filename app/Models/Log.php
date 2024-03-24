<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Concerns\Filterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory, Filterable;

    protected $table = 'applogs';

    protected $fillable = [
        'user_id',
        'created',
        'updated',
        'deleted',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

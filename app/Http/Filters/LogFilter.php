<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;


class LogFilter extends Filter
{
    public function from_date(string $value = null): Builder
    {
        $date = date('Y-m-d', strtotime($value));
        return $this->builder->where('created_at', '>=', $date);
    }

    public function to_date(string $value = null): Builder
    {
        $date = date('Y-m-d', strtotime($value));
        return $this->builder->where('created_at', '<=', $date);
    }
    
    public function item(string $value = null): Builder
    {
        return $this->builder
        ->where('created', 'like', '%{$value}%')
        ->orWhere('updated', 'like', '%{$value}%')
        ->orWhere('deleted', 'like', '%{$value}%');
    }

    public function user(string $value = null): Builder
    {
        return $this->builder->where('user_id', '=', $value);
    }
}
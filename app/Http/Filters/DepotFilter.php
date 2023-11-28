<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class DepotFilter extends Filter
{
    public function description(string $value = null): Builder
    {
        return $this->builder->where('description', 'like', "{$value}%");
    }

    
    public function number(string $value = null): Builder
    {
        return $this->builder->where('inventory_number', $value);
    }

    
    public function sort(array $value = []): Builder
    {
        if (isset($value['by']) && ! Schema::hasColumn('products', $value['by'])) {
            return $this->builder;
        }

        return $this->builder->orderBy(
            $value['by'] ?? 'created_at', $value['order'] ?? 'desc'
        );
    }
}
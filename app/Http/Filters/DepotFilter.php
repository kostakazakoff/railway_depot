<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;


class DepotFilter extends Filter
{
    public function description(string $value = null): Builder
    {
        return $this->builder->where('description', 'like', "%{$value}%");
    }

    
    public function number(string $value = null): Builder
    {
        return $this->builder->where('inventory_number', 'like', "%{$value}%");
    }

    
    public function sort(array $value = []): Builder
    {
        if (isset($value['by']) && ! Schema::hasColumn('articles', $value['by'])) {
            return $this->builder;
        }

        return $this->builder->orderBy(
            $value['by'] ?? 'created_at', $value['order'] ?? 'desc'
        );
    }
}

/*
/articles?sort[by]=price&sort[order]=asc&store=1&description=Arti&number=1
*/
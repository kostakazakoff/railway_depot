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

    public function inventory_number(string $value = null): Builder
    {
        return $this->builder->where('inventory_number', '=', $value);
    }

    public function draft_number(string $value = null): Builder
    {
        return $this->builder->where('draft_number', '=', $value);
    }

    public function catalog_number(string $value = null): Builder
    {
        return $this->builder->where('catalog_number', '=', $value);
    }

    public function material(string $value = null): Builder
    {
        return $this->builder->where('material', 'like', "%{$value}%");
    }

    public function min_price(string $value = null): Builder
    {
        return $this->builder->where('price', '>=', $value);
    }

    public function max_price(string $value = null): Builder
    {
        return $this->builder->where('price', '<=', $value);
    }

    public function from_date(string $value = null): Builder
    {
        $date = date('Y-m-d', strtotime($value));
        return $this->builder->where('created_at', '>=', $date);
    }

    public function to_date(string $value = null): Builder
    {
            return $this->builder->where('created_at', '<=', $value);
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
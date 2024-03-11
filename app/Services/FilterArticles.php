<?php

namespace App\Services;

class FilterArticles
{
    public static function by($articles, $request)
    {
        $store = $request->query->get('store');
        $min_quantity = $request->query->get('min_quantity');
        $max_quantity = $request->query->get('max_quantity');
        $position = $request->query->get('position');
        $package = $request->query->get('package');

        $store &&
            $articles = $articles
            ->filter(function ($article) use ($store) {
                return $article->stores[0]->id == $store;
            });

        $min_quantity &&
            $articles = $articles
            ->filter(function ($article) use ($min_quantity) {
                return $article->inventory->quantity >= $min_quantity;
            });

        $max_quantity &&
            $articles = $articles
            ->filter(function ($article) use ($max_quantity) {
                return $article->inventory->quantity <= $max_quantity;
            });

        $position &&
            $articles = $articles
            ->filter(function ($article) use ($position) {
                return strstr($article->inventory->position, $position);
            });

        $package &&
            $articles = $articles
            ->filter(function ($article) use ($package) {
                return strstr($article->inventory->package, $package);
            });

        return $articles;
    }
}
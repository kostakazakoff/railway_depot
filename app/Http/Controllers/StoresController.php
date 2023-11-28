<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Inventory;
use App\Models\Store;
use Illuminate\Http\JsonResponse;


class StoresController extends Controller
{
    public function list(): JsonResponse
    {
        $articles = collect(Store::all());

        return response()->json($articles);
    }


    public function show($id): ?JsonResponse
    {
        $store = Store::find($id);

        return  response()->json($store);
    }


    public function depotInventories($id): JsonResponse
    {
        $inventories = Store::find($id)?->articles;

        $total = collect(Inventory::whereStoreId($id)->get())
            ->pluck('quantity', 'article_id')
            ->reduce(function ($sum, $quantity, $article_id) {
                $price = Article::find($article_id)?->price;
                return $sum + $price * $quantity;
            }, 0);

        return response()->json(['inventories' => $inventories, 'total' => $total]);
    }
}

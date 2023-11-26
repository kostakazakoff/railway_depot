<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Inventory;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


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

        return response()->json($inventories);
    }


    public function depotTotal($id): JsonResponse
    {
        $inventory = collect(Inventory::whereStoreId($id)->get());
        $total = $inventory

            ->pluck('article_id', 'quantity')

            ->reduce(function ($sum, $article_id, $quantity) {
                $price = Article::find($article_id)?->price;
                return $sum + $price * $quantity;
            }, 0);

        return response()->json($total);
    }
}

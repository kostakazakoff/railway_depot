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


    public function depotInventoriesValue($id): JsonResponse
    {
        $total = collect(Inventory::whereStoreId($id)
        ->pluck('article_id', 'quantity'))
        ->reduce(function ($sum, $article_id, $quantity) {
            $price = Article::find($article_id)->price;
            return $sum + $price * $quantity;
        }, 0);
        
        // $total = 0;
        // foreach ($inventories as $quantity => $article_id) {
        //     $price = Article::find($article_id)->price;
        //     $total += $quantity * floatval($price);
        // }

        return response()->json($total);
    }
}

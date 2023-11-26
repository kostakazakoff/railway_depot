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
        $value = 0;

        $inventories = collect(Inventory::whereStoreId($id)->pluck('article_id', 'quantity'));
        
        foreach ($inventories as $quantity => $article_id) {
            $price = Article::find($article_id)->price;
            $value += $quantity * floatval($price);
        }

        return response()->json($value);
    }
}

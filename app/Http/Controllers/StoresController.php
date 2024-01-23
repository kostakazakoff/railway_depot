<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Image;
use App\Models\Inventory;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class StoresController extends Controller
{
    public function list(): JsonResponse
    {
        $stores = collect(Store::all());

        return response()->json($stores);
    }


    public function depotInventories($id): JsonResponse
    {
        $inventories = Inventory::whereStoreId($id)->get();

        $store = Store::whereId($id)->get();

        foreach ($inventories as $key => $inventory) {
            $article = Article::whereId($inventory->article_id)->get();

            $inventory['article'] = $article;

            $images = Image::whereArticleId($inventory->article_id)->get();
            $images && $inventory['images'] = $images;
        }

        $totalCost = collect(Inventory::whereStoreId($id)->get())
            ->pluck('quantity', 'article_id')
            ->reduce(function ($sum, $quantity, $article_id) {
                $price = Article::find($article_id)?->price;
                return $sum + $price * $quantity;
            }, 0);

        return response()->json(['inventories' => $inventories, 'store' => $store, 'totalCost' => $totalCost]);
    }


    // public function depotInventories($id): JsonResponse
    // {
    //     $inventories = DB::table('inventories')
    //         ->leftJoin('articles', 'inventories.article_id', '=', 'articles.id')
    //         ->where('articles.deleted_at', '=', null)
    //         ->leftJoin('stores', 'inventories.store_id', '=', 'stores.id')
    //         ->where('stores.id', '=', $id)
    //         ->select(
    //             'description',
    //             'article_id',
    //             'quantity',
    //             'price',
    //             'position',
    //             'package',
    //             'inventory_number',
    //             'catalog_number',
    //             'draft_number',
    //             'material',
    //             'stores.name'
    //         )
    //         ->get();

    //     $total = collect(Inventory::whereStoreId($id)->get())
    //         ->pluck('quantity', 'article_id')
    //         ->reduce(function ($sum, $quantity, $article_id) {
    //             $price = Article::find($article_id)?->price;
    //             return $sum + $price * $quantity;
    //         }, 0);

    //     return response()->json(['inventories' => $inventories, 'total_inventories_cost' => $total]);
    // }
}

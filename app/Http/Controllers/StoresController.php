<?php

namespace App\Http\Controllers;

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

        $inventories = collect(Store::find($id)?->articles);

        foreach ($inventories as $article) {
            $quantity = DB::select(
                'select quantity from inventories where article_id = ? and store_id = ?',
                [$article->id, $id]
            );
            
            if ($quantity) {
                $value += $quantity[0]->quantity * floatval($article->price);
            }
        }

        return response()->json($value);
    }
}

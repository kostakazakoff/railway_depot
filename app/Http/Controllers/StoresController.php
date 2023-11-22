<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StoresController extends Controller
{
    public function list(): JsonResponse
    {
        $articles = collect(Store::all());

        return response()->json(['success' => true, 'result' => $articles]);
    }


    public function show($id): ?JsonResponse
    {
        $store = Store::findOrFail($id);

        return  response()->json(['success' => true, 'result' => $store]);
    }


    public function storeInventories($id): JsonResponse
    {
        $inventories = Store::find($id)->articles;

        count($inventories) == 0
            ? $success = false
            : $success = true;

        return response()->json(['success' => $success, 'result' => $inventories]);
    }


    public function storeInventoriesValue($id): JsonResponse
    {
        $value = 0;

        $inventories = collect(Store::find($id)->articles);

        foreach ($inventories as $article) {
            $quantity = DB::select(
                'select quantity from inventories where article_id = ? and store_id = ?',
                [$article->id, $id]);

            if ($quantity) {
                $value += $quantity[0]->quantity * intval($article->price);
            }
        }

        return response()->json($value);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

    public function storeInventories($id): ?JsonResponse
    {
        $success = false;
        $inventories = Store::find($id)->articles;
        count($inventories) == 0
        ? $success = false
        : $success = true;

        return response()->json(['success' => $success, 'result' => $inventories]);
    }
}

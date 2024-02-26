<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\JsonResponse;


class StoresController extends Controller
{
    public function list(): JsonResponse
    {
        $stores = collect(Store::all());

        return response()->json($stores);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStoreRequest;
use App\Models\Store;
use Illuminate\Http\JsonResponse;


class StoresController extends Controller
{
    const SUCCESS = 'success';

    public function list(): JsonResponse
    {
        $stores = collect(Store::all());

        return response()->json($stores);
    }


    public function create(CreateStoreRequest $request): JsonResponse
    {
        $store = Store::create([
            'name' => $request->name
        ]);

        return response()->json(['message' => self::SUCCESS, 'store' => $store]);
    }


    public function edit(CreateStoreRequest $request, string $id): JsonResponse
    {
        $store = Store::findOrFail($id);

        $store->update($request->name);

        return response()->json(['message' => self::SUCCESS, 'store' => $store]);
    }


    public function delete(string $id)
    {
        $store = Store::findOrFail($id);

        $store->delete();

        return response()->json(['message' => self::SUCCESS]);
    }
}

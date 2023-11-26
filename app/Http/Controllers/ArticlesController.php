<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\StoreImagesRequest;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\Image;
use App\Models\Inventory;
use Illuminate\Support\Facades\Artisan;

/* TODO: Filtering */

class ArticlesController extends Controller
{

    public function list(): JsonResponse
    {
        $articles = collect(Article::all());
        $deleted = Article::onlyTrashed()->get();

        return response()->json(['success' => true, 'result' => $articles, 'trashed' => $deleted]);
    }


    private function uploadImages($imageRequest, $articleId): void
    {
        foreach ($imageRequest as $image) {

            $imageName = time() . '_' . $image->getClientOriginalName();

            $imageLocation = storage_path('app/images');

            $image->move($imageLocation, $imageName);

            Image::create([
                'filename' => $imageName,
                'path' => $imageLocation . '/' . $imageName,
                'article_id' => $articleId
            ]);
        }
    }


    private function handleOrder($request, $imgRequest, $inventoryRequest)
    {
        $article = Article::create([
            'inventory_number' => $request->inventory_number,
            'catalog_number' => $request->catalog_number,
            'draft_number' => $request->draft_number,
            'material' => $request->material,
            'description' => $request->description,
            'price' => $request->price
        ]);

        $inventory = Inventory::create([
            'article_id' => $article->id,
            'store_id' => $inventoryRequest->store_id,
            'quantity' => $inventoryRequest->quantity,
            'package' => $inventoryRequest->package,
            'position' => $inventoryRequest->position
        ]);

        $imageRequest = $imgRequest->file('images');

        if ($imageRequest) {
            $this->uploadImages($imageRequest, $article->id);
        }

        return ['article' => $article, 'inventory' => $inventory];
    }


    public function store(
        StoreArticleRequest $request,
        StoreImagesRequest $imgRequest,
        StoreInventoryRequest $inventoryRequest
    ): JsonResponse {

        $response = $this->handleOrder($request, $imgRequest, $inventoryRequest);

        return response()->json($response);
    }


    public function show(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        return response()->json($article);
    }


    /* TODO: */
    public function getTrashed(): JsonResponse
    {
        $deleted = Article::onlyTrashed()->get();

        return response()->json($deleted);
    }


    public function update(StoreArticleRequest $request, StoreImagesRequest $imgRequest, string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $article->update($request->except('images'));

        $imageRequest = $imgRequest->file('images');

        if ($imageRequest) {
            $this->uploadImages($imageRequest, $article->id);
        }

        return response()->json($article);
    }


    public function delete($id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $article->delete();

        return response()->json('Article deleted successfully');
    }


    public function articleInventories($id): JsonResponse
    {
        $inventories = collect(Article::find($id)?->stores);

        return response()->json($inventories);
    }


    public function pruneModel(): JsonResponse
    {
        Artisan::call('model:prune');

        return response()->json('Confirmed');
    }
}

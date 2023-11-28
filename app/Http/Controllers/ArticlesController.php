<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Article;
use App\Models\Inventory;
use App\Http\Filters\DepotFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\StoreImagesRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateArticleRequest;

/* TODO: Filtering */
class ArticlesController extends Controller
{
    public function list(DepotFilter $filter): JsonResponse
    {
        $articles = Article::filter($filter)->get();

        $deleted = Article::onlyTrashed()->get();

        return response()->json(['success' => true, 'articles' => $articles, 'trashed' => $deleted]);
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


    private function handleImages($article, $imgRequest, $inventoryRequest): void
    {
        $imageRequest = $imgRequest->file('images');

        if ($imageRequest) {
            $this->uploadImages($imageRequest, $article->id);
        };
    }


    public function store(
        StoreArticleRequest $request,
        StoreImagesRequest $imgRequest,
        StoreInventoryRequest $inventoryRequest
    ): JsonResponse
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

        $this->handleImages($article, $imgRequest, $inventoryRequest);

        return response()->json(['success' => true, 'article' => $article, 'inventory' => $inventory]);
    }


    public function update(
        UpdateArticleRequest $request,
        StoreImagesRequest $imgRequest,
        StoreInventoryRequest $inventoryRequest,
        string $id
    ): ?JsonResponse {
        $article = Article::findOrFail($id);

        $article->update([
            // 'inventory_number' => $request->inventory_number,
            'catalog_number' => $request->catalog_number,
            'draft_number' => $request->draft_number,
            'material' => $request->material,
            'description' => $request->description,
            'price' => $request->price
        ]);

        $inventory = Inventory::whereArticleId($id);

        $inventory->update([
            'store_id' => $inventoryRequest->store_id,
            'quantity' => $inventoryRequest->quantity,
            'package' => $inventoryRequest->package,
            'position' => $inventoryRequest->position
        ]);

        $this->handleImages($article, $imgRequest, $inventoryRequest);

        return response()->json(['success' => true, 'article' => $article, 'inventory' => $inventory]);
    }


    public function show(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $articleImages = $article->images->all();

        return response()->json(['success' => true, 'article' => $article, 'images' => $articleImages]);
    }


    public function delete($id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $article->delete();

        return response()->json('Article deleted successfully');
    }


    public function pruneModel(): JsonResponse
    {
        Artisan::call('model:prune');

        return response()->json('Confirmed');
    }
}

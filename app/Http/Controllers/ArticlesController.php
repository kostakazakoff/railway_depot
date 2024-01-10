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
use App\Http\Requests\UpdateInventoryRequest;

class ArticlesController extends Controller
{
    const IMAGES_DIR = 'images';

    public function list(DepotFilter $filter): JsonResponse
    {
        $articles = Article::filter($filter)->get();

        $deleted = Article::onlyTrashed()->get();

        return response()->json(['success' => true, 'articles' => $articles, 'trashed' => $deleted]);
    }


    private function uploadImages($images, $articleId): void
    {
        foreach ($images as $image) {

            $imageName = time() . '_' . $image->getClientOriginalName();

            $imageLocation = public_path(self::IMAGES_DIR);

            $image->move($imageLocation, $imageName);

            $url = asset(self::IMAGES_DIR.'/'.$imageName);

            Image::create([
                'path' => $imageLocation . '/' . $imageName,
                'url' => $url,
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
    ): JsonResponse {

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
        UpdateInventoryRequest $inventoryRequest,
        string $id
    ): ?JsonResponse {

        $article = Article::findOrFail($id);

        $article_request = [
            'inventory_number' => $request->inventory_number,
            'catalog_number' => $request->catalog_number,
            'draft_number' => $request->draft_number,
            'material' => $request->material,
            'description' => $request->description,
            'price' => $request->price
        ];


        foreach ($article_request as $field => $value) {
            if ($value) $article->$field = $value;
        }
        

        $article->save();

        $inventory = Inventory::whereArticleId($id)->first();

        $inventory_request = [
            'store_id' => $inventoryRequest->store_id,
            'quantity' => $inventoryRequest->quantity,
            'package' => $inventoryRequest->package,
            'position' => $inventoryRequest->position
        ];

        foreach ($inventory_request as $field => $value) {
            if ($value) $inventory->$field = $value;
        }

        $inventory->save();

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


    public function emptyTrash(): JsonResponse
    {
        Artisan::call('model:prune');

        return response()->json('Trash is empty');
    }
}

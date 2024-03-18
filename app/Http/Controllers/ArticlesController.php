<?php

namespace App\Http\Controllers;

use App\Events\ArticleCRUD;
use App\Models\Image;
use App\Models\Article;
use App\Models\Inventory;
use App\Http\Filters\DepotFilter;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreImagesRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\Store;
use Symfony\Component\HttpFoundation\Request;
use App\Services\FilterArticles;


class ArticlesController extends Controller
{
    const IMAGES_DIR = 'images';
    const SUCCESS = 'success';

    public function list(Request $request, DepotFilter $filter): JsonResponse
    {
        $totalCost = 0;

        $userResponsibility =  auth()->user()->stores->pluck('id');

        $articles = Article::filter($filter)
            ->with('images')
            ->with('stores')
            ->with('inventory')
            ->get();

        foreach ($articles as $article) {
            if (!$article->inventory) {
                $article->delete();
            }
        }

        $filteredArticles = FilterArticles::by($articles, $request);

        foreach ($filteredArticles as $article) {
                $totalCost += $article->price * $article->inventory->quantity;
            
        }

        return response()->json([
            'message' => self::SUCCESS,
            'articles' => [...$filteredArticles],
            'totalCost' => $totalCost
        ]);
    }


    protected function uploadImages($images, $articleId): void
    {
        foreach ($images as $image) {

            $imageName = time() . '_' . $image->getClientOriginalName();

            $imageLocation = public_path(self::IMAGES_DIR);

            $image->move($imageLocation, $imageName);

            $url = asset(self::IMAGES_DIR . '/' . $imageName);

            Image::create([
                'path' => $imageLocation . '/' . $imageName,
                'url' => $url,
                'article_id' => $articleId
            ]);
        }
    }


    protected function handleImages($article, $imgRequest): void
    {
        $files = $imgRequest->file('images');

        if ($files) {
            $this->uploadImages($files, $article->id);
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

        $this->handleImages($article, $imgRequest);

        ArticleCRUD::dispatch($article, 'created');

        return response()->json([
            'message' => self::SUCCESS,
            'article' => $article,
            'inventory' => $inventory
        ]);
    }


    public function update(
        StoreArticleRequest $request,
        StoreImagesRequest $imgRequest,
        StoreInventoryRequest $inventoryRequest,
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
            $article->$field = $value;
        }

        !$article->quantity <= 0
            ? $article->delete()
            : $article->save();

        $inventory = Inventory::whereArticleId($id)->first();

        $inventory_request = [
            'store_id' => $inventoryRequest->store_id,
            'quantity' => $inventoryRequest->quantity,
            'package' => $inventoryRequest->package,
            'position' => $inventoryRequest->position
        ];

        foreach ($inventory_request as $field => $value) {
            $inventory->$field = $value;
        }

        $inventory->save();

        $this->handleImages($article, $imgRequest, $inventoryRequest);

        ArticleCRUD::dispatch($article, 'updated');

        return response()->json([
            'message' => self::SUCCESS,
            'article' => $article,
            'inventory' => $inventory
        ]);
    }


    public function show(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $articleInventory = Inventory::whereArticleId($id)->get();

        $articleImages = $article->images?->all();

        return response()->json([
            'message' => self::SUCCESS,
            'article' => $article,
            'images' => $articleImages,
            'inventory' => $articleInventory
        ]);
    }


    public function delete($id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        ArticleCRUD::dispatch($article, 'deleted');

        $article->delete();

        return response()->json(['message' => self::SUCCESS]);
    }
}

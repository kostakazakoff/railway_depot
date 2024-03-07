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
use Symfony\Component\HttpFoundation\Request;
use App\Exceptions\AppException;
use App\Models\Store;
use App\Services\LogsMaker;

class ArticlesController extends Controller
{
    const IMAGES_DIR = 'images';
    const SUCCESS = 'success';

    public function list(Request $request, DepotFilter $filter): JsonResponse
    {
        $store = $request->query->get('store');
        $min_quantity = $request->query->get('min_quantity');
        $max_quantity = $request->query->get('max_quantity');
        $position = $request->query->get('position');
        $package = $request->query->get('package');

        $articles = Article::filter($filter)
            ->with('images')
            ->with('stores')
            ->get();

        if ($articles->isEmpty()) {
            return response()->json([
                'message' => AppException::notFound('артикули')->getMessage(),
                'status' => AppException::notFound('артикули')->getCode()
            ]);
        }

        $totalCost = 0;

        foreach ($articles as $article) {
            $inventory = Inventory::whereArticleId($article->id)->first();
            $inventory
                ? $article['inventory'] = $inventory
                : $article['inventory'] = null;
        }

        $store &&
            $articles = $articles
            ->filter(function ($article) use ($store) {
                return $article->stores[0]->id == $store;
            });

        $min_quantity &&
            $articles = $articles
            ->filter(function ($article) use ($min_quantity) {
                return $article->inventory->quantity >= $min_quantity;
            });

        $max_quantity &&
            $articles = $articles
            ->filter(function ($article) use ($max_quantity) {
                return $article->inventory->quantity <= $max_quantity;
            });

        $position &&
            $articles = $articles
            ->filter(function ($article) use ($position) {
                return strstr($article->inventory->position, $position);
            });

        $package &&
            $articles = $articles
            ->filter(function ($article) use ($package) {
                return strstr($article->inventory->package, $package);
            });

        foreach ($articles as $article) {
            $totalCost += $article->price * $article->inventory->quantity;
        }

        return response()->json([
            'message' => self::SUCCESS,
            'articles' => [...$articles],
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
            $value && $article->$field = $value;
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
            if ($value) $inventory->$field = $value;
        }

        $inventory->save();

        $this->handleImages($article, $imgRequest, $inventoryRequest);

        return response()->json([
            'message' => self::SUCCESS,
            'article' => $article,
            'inventory' => $inventory
        ]);
    }


    public function show(string $id): ?JsonResponse
    {
        $article = Article::find($id);

        $articleInventory = Inventory::whereArticleId($id)->get();

        $articleImages = $article?->images->all();

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

        $inventory = Inventory::whereArticleId($id)->first();

        $store = Store::find($inventory->store_id);

        $article->delete();

        LogsMaker::log('deleted', $inventory, $article, $store);

        return response()->json(['message' => self::SUCCESS]);
    }
}

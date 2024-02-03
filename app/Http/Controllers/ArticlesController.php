<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Article;
use App\Models\Inventory;
use App\Models\Store;
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


    // public function list(DepotFilter $filter, Request $request): JsonResponse
    // {
    //     $articles = DB::table('inventories', 'i')
    //         // ->leftJoin('inventories', 'articles.id', '=', 'inventories.article_id')
    //         ->leftJoin('articles', 'i.article_id', '=', 'articles.id')
    //         ->leftJoin('stores', 'i.store_id', '=', 'stores.id')
    //         ->select(
    //             'i.article_id',
    //             'articles.description',
    //             'articles.inventory_number',
    //             'articles.catalog_number',
    //             'articles.draft_number',
    //             'articles.material',
    //             'articles.price',
    //             'i.quantity',
    //             'i.package',
    //             'i.store_id',
    //             'i.position',
    //             'stores.name'
    //         )
    //         ->where('articles.deleted_at', '=', null)
    //         ->when(request('inventory_number'), function ($query, $description) {
    //             return $query->where('inventory_number', 'like', '%' . $description . '%');
    //         })
    //         ->when(request('description'), function ($query, $description) {
    //             return $query->where('description', 'like', '%' . $description . '%');
    //         })
    //         ->when(request('catalog_number'), function ($query, $description) {
    //             return $query->where('catalog_number', 'like', '%' . $description . '%');
    //         })
    //         ->when(request('draft_number'), function ($query, $description) {
    //             return $query->where('draft_number', 'like', '%' . $description . '%');
    //         })
    //         ->when(request('material'), function ($query, $description) {
    //             return $query->where('material', 'like', '%' . $description . '%');
    //         })
    //         ->when(request('package'), function ($query, $description) {
    //             return $query->where('package', 'like', '%' . $description . '%');
    //         })
    //         ->when(request('position'), function ($query, $description) {
    //             return $query->where('position', 'like', '%' . $description . '%');
    //         })
    //         ->when(request('min_price'), function ($query, $description) {
    //             return $query->where('price', '>=', $description);
    //         })
    //         ->when(request('max_price'), function ($query, $description) {
    //             return $query->where('price', '<=', $description);
    //         })
    //         ->when(request('min_quantity'), function ($query, $description) {
    //             return $query->where('quantity', '>=', $description);
    //         })
    //         ->when(request('max_quantity'), function ($query, $description) {
    //             return $query->where('quantity', '<=', $description);
    //         })
    //         ->get();

    //     return response()->json(['articles' => $articles]);
    // }


    public function list(DepotFilter $filter): JsonResponse
    {
        $articles = Article::filter($filter)->get();
        $totalCost = 0;

        foreach ($articles as $article) {
            $article->images
            ? $article['images'] = $article->images
            : $article['images'] = [];

            $inventory = Inventory::whereArticleId($article->id)->first();
            $inventory
            ? $article['inventory'] = $inventory
            : $article['inventory'] = null;

            $store = Store::whereId($article->inventory->store_id)->first();
            $store
            ? $article['store'] = $store
            : $article['store'] = null;

            $totalCost += $article->price * $article->inventory->quantity;
        }
        
        return response()->json(['articles' => $articles, 'totalCost' => $totalCost]);
    }


    private function uploadImages($images, $articleId): void
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

        return response()->json(['article' => $article, 'inventory' => $inventory]);
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

        return response()->json(['article' => $article, 'inventory' => $inventory]);
    }


    public function show(string $id): ?JsonResponse
    {
        $article = Article::find($id);

        $articleInventory = Inventory::whereArticleId($id)->get();

        $articleImages = $article->images->all();

        return response()->json(['article' => $article, 'images' => $articleImages, 'inventory' => $articleInventory]);
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


    public function restoreArticle($id): JsonResponse
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->deleted_at = null;

        $article->save();

        return response()->json('Article '.$article->slug.' restored successfully');
    }


    public function getTrashed(): JsonResponse
    {
        $trashedArticles = Article::onlyTrashed()->get();
        dd($trashedArticles);

        return response()->json(['trashed' => $trashedArticles]);
    }
}

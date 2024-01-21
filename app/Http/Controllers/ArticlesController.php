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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ArticlesController extends Controller
{
    const IMAGES_DIR = 'images';


    public function list(DepotFilter $filter, Request $request): JsonResponse
    {
        $articles = DB::table('articles')
            ->leftJoin('inventories', 'articles.id', '=', 'inventories.article_id')
            ->select(
                'inventories.article_id',
                'description',
                'inventory_number',
                'catalog_number',
                'draft_number',
                'material',
                'price',
                'quantity',
                'package',
                'store_id',
                'position',
            )
            ->where('articles.deleted_at', '=', null)
            ->when(request('inventory_number'), function ($query, $description) {
                return $query->where('inventory_number', 'like', '%'.$description.'%');
            })
            ->when(request('description'), function ($query, $description) {
                return $query->where('description', 'like', '%'.$description.'%');
            })
            ->when(request('catalog_number'), function ($query, $description) {
                return $query->where('catalog_number', 'like', '%'.$description.'%');
            })
            ->when(request('draft_number'), function ($query, $description) {
                return $query->where('draft_number', 'like', '%'.$description.'%');
            })
            ->when(request('material'), function ($query, $description) {
                return $query->where('material', 'like', '%'.$description.'%');
            })
            ->when(request('package'), function ($query, $description) {
                return $query->where('package', 'like', '%'.$description.'%');
            })
            ->when(request('position'), function ($query, $description) {
                return $query->where('position', 'like', '%'.$description.'%');
            })
            ->when(request('min_price'), function ($query, $description) {
                return $query->where('price', '>=', $description);
            })
            ->when(request('max_price'), function ($query, $description) {
                return $query->where('price', '<=', $description);
            })
            ->when(request('min_quantity'), function ($query, $description) {
                return $query->where('quantity', '>=', $description);
            })
            ->when(request('max_quantity'), function ($query, $description) {
                return $query->where('quantity', '<=', $description);
            })
            ->get();

        return response()->json(['articles' => $articles]);
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
}

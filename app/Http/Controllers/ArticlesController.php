<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\StoreImagesRequest;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

/* TODO: Filtering */

class ArticlesController extends Controller
{

    public function list(): JsonResponse
    {
        $articles = collect(Article::all());

        return response()->json(['success' => true, 'result' => $articles]);
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


    public function store(StoreArticleRequest $request, StoreImagesRequest $imgRequest): JsonResponse
    {
        $article = Article::create($request->except('image'));

        $imageRequest = $imgRequest->file('images');

        if ($imageRequest) {
            $this->uploadImages($imageRequest, $article->id);
        }

        return response()->json($article);
    }


    public function show(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        return response()->json($article);
    }


    public function showDeleted(): JsonResponse
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
}

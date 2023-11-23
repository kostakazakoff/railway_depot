<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreArticleRequest;
use App\Models\Image;

/* TODO:
Filtering
*/

class ArticlesController extends Controller
{

    public function list(): JsonResponse
    {
        $articles = collect(Article::all());

        return response()->json(['success' => true, 'result' => $articles]);
    }


    /* TODO: Validate files */
    private function uploadImages($imageRequest, $article): void
    {
        foreach ($imageRequest as $image) {
            
            $imageName = time() . '_' . $image->getClientOriginalName();

            $imageLocation = public_path('images');

            $image->move($imageLocation, $imageName);

            Image::create([
                'filename' => $imageName,
                'path' => $imageLocation . '/' . $imageName,
                'article_id' => $article->id
            ]);
        }
    }

    /* TODO: */
    private function updateImages($imageRequest, $article): void
    {
        foreach ($imageRequest as $image) {
            dd($article->images);
        }
    }


    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = Article::create($request->except('image'));

        $imageRequest = $request->file('images');

        if ($imageRequest) {
            $this->uploadImages($imageRequest, $article);
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

    
    /* TODO: */
    public function update(StoreArticleRequest $request, string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);
        
        $article->update($request->except('images'));

        $imageRequest = $request->file('images');

        if ($imageRequest) {
            $this->updateImages($imageRequest, $article);
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

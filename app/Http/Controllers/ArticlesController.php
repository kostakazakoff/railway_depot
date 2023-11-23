<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Image;
use Illuminate\Http\Request;

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
    private function handleImages($imageRequest, $articleId): void
    {
        foreach ($imageRequest as $image) {
            
            $imageName = time() . '_' . $image->getClientOriginalName();

            $imageLocation = public_path('images');

            $image->move($imageLocation, $imageName);

            Image::create([
                'filename' => $imageName,
                'path' => $imageLocation . '/' . $imageName,
                'article_id' => $articleId
            ]);
        }
    }


    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = Article::create($request->except('image'));

        $imageRequest = $request->file('images');

        if ($imageRequest) {
            $this->handleImages($imageRequest, $article->id);
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


    public function update(Request $request, string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);
        
        $article->update($request->except('images'));

        $imageRequest = $request->file('images');

        if ($imageRequest) {
            $this->handleImages($imageRequest, $article->id);
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

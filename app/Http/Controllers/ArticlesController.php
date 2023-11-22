<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequest;
use App\Models\Image;
use Illuminate\Support\Facades\File;

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


    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = Article::create($request->except('image'));

        $imageRequest = $request->file('images');

        if (!$imageRequest) {
            return response()->json($article);
        }

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
        count($deleted) == 0
            ? $success = false
            : $success = true;

        return response()->json(['success' => $success, 'result' => $deleted]);
    }


    public function edit(StoreArticleRequest $request, string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $article->update($request->all());

        return response()->json($article);
    }


    public function delete(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        // $files = $article->images;

        // if ($files) {
        //     foreach ($files as $file) {
        //         Image::whereFilename($file->filename)->delete();
        //         File::delete($file->path);
        //     }
        // }

        $article->delete();

        return response()->json('Article deleted successfully');
    }


    public function articleInventories($id): JsonResponse
    {
        $inventories = collect(Article::find($id)?->stores);

        return response()->json($inventories);
    }
}

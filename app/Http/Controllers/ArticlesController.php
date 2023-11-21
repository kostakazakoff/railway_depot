<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequest;


/* TODO:
Validations (StoreRequest)
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
        $article = Article::create($request->all());

        return response()->json(['success' => true, 'result' => $article]);
    }

    public function show(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        return response()->json(['success' => true, 'result' => $article]);
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

        return response()->json(['success' => true, 'result' => $article]);
    }

    public function delete(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $article->delete();

        return response()->json(['success' => true]);
    }

    public function articleInventories($id): JsonResponse
    {
        $success = false;
        $inventories = Article::find($id)->stores;
        count($inventories) == 0
            ? $success = false
            : $success = true;

        return response()->json(['success' => $success, 'result' => $inventories]);
    }
}

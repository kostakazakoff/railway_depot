<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


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

    public function store(Request $request): JsonResponse
    {
        $article = Article::create($request->all());

        return response()->json(['success' => true, 'article' => $article]);
    }

    public function show(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        return response()->json(['success' => true, 'article' => $article]);
    }

    public function edit(Request $request, string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $article->update($request->all());

        return response()->json(['success' => true, 'article' => $article]);
    }

    public function delete(string $id): ?JsonResponse
    {
        $article = Article::findOrFail($id);

        $article->delete();

        return response()->json(['success' => true]);
    }

    public function articleInventories($id): ?JsonResponse
    {
        $success = false;
        $inventories = Article::find($id)->stores;
        count($inventories) == 0
        ? $success = false
        : $success = true;

        return response()->json(['success' => $success, 'result' => $inventories]);
    }
}

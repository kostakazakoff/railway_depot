<?php

namespace App\Observers;

use App\Models\Image;
use App\Models\Article;
use App\Models\Inventory;
use App\Models\Log;
use App\Models\Store;
use App\Services\LogsMaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleObserver
{
    // protected function logData($article)
    // {
    //     $inventory = Inventory::whereArticleId($article->id)->first();

    //     $store = Store::find($inventory->store_id);

    //     return [$inventory, $article, $store];
    // }

    public function created(Article $article): void
    {
        //
    }


    public function updated(Article $article): void
    {
        //
    }


    public function deleted(Article $article): void
    {
        // $dataForLoging = $this->logData($article);

        // LogsMaker::log('deleted', ...$dataForLoging);

        $inventoryToDelete = DB::table('inventories')
            ->where('inventories.article_id', $article->id);

        $inventoryToDelete->delete();

        $files = $article->images;

        if ($files) {
            foreach ($files as $file) {
                $image = Image::find($file->id);
                Storage::delete(($image->path));
                $image->delete();
            }
        }

        // Log::create([
        //     'user_id' => auth()->user()->id,
        //     'deleted' => $article->description
        // ]);
    }


    public function restored(Article $article): void
    {
        //
    }


    public function forceDeleted(Article $article): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Models\Image;
use App\Models\Article;
use App\Models\Inventory;
use Illuminate\Support\Facades\Storage;

class ArticleObserver
{
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
        $inventoryToDelete = Inventory::where('inventories.article_id', $article->id);

        $inventoryToDelete->delete();

        $files = $article->images;

        if ($files) {
            foreach ($files as $file) {
                $image = Image::find($file->id);
                Storage::delete(($image->path));
                $image->delete();
            }
        }
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

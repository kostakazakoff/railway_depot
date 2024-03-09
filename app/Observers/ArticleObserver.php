<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Image;
use App\Models\Inventory;
use Illuminate\Support\Facades\Storage;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        $inventoryToDelete = Inventory::where('inventories.article_id', $article->id);

        $inventoryToDelete?->delete();

        $files = $article->images;

        if ($files) {
            foreach ($files as $file) {
                $image = Image::find($file->id);
                Storage::delete(($image->path));
                $image->delete();
            }
        }
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        //
    }
}

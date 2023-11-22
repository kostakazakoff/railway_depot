<?php

namespace App\Observers;

use App\Models\Image;
use App\Models\Article;
use Illuminate\Support\Facades\File;

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
        $files = $article->images;

        if ($files) {
            foreach ($files as $file) {
                Image::whereFilename($file->filename)->delete();
                File::delete($file->path);
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

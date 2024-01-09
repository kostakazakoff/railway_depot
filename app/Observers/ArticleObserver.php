<?php

namespace App\Observers;

use App\Models\Image;
use App\Models\Article;
use Illuminate\Support\Facades\File;

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
        $files = $article->images;

        if ($files) {
            foreach ($files as $file) {
                Image::find($file->id)->delete();
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

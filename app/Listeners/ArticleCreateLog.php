<?php

namespace App\Listeners;

use App\Services\ArticlesLogsMaker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ArticleCreateLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }
    
    public function handle(object $event): void
    {
        $operation = $event->operation;
        $article = $event->article;

        ArticlesLogsMaker::log($operation, $article);
    }
}

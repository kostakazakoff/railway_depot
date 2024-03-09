<?php

namespace App\Listeners;

use App\Models\Inventory;
use App\Models\Store;
use App\Services\LogsMaker;
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

        LogsMaker::log($operation, $article);
    }
}

<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Inventory;
use App\Models\Log;
use App\Models\Store;
use App\Services\LogsMaker;

class InventoryObserver
{
    protected function logData($inventory)
    {
        $article = Article::find($inventory->article_id);
        $store = Store::find($inventory->store_id);

        return [$inventory, $article, $store];
    }

    public function created(Inventory $inventory): void
    {
        LogsMaker::log('created', ...$this->logData($inventory));
    }

    public function updated(Inventory $inventory): void
    {
        LogsMaker::log('updated', ...$this->logData($inventory));
    }

    public function deleted(Inventory $inventory): void
    {
        //
    }

    /**
     * Handle the Inventory "restored" event.
     */
    public function restored(Inventory $inventory): void
    {
        //
    }

    /**
     * Handle the Inventory "force deleted" event.
     */
    public function forceDeleted(Inventory $inventory): void
    {
        //
    }
}

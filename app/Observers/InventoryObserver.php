<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Inventory;
use App\Models\Log;
use App\Models\Store;

class InventoryObserver
{
    protected function handle($inventory, $operation)
    {
        $article = Article::find($inventory->article_id);
        $store = Store::find($inventory->store_id);

        Log::create([
            'user_id' => auth()->user()->id,
            $operation => $article->description.' с инвентарен номер '
                . $article->inventory_number
                . ', цена '
                . $article->price
                . ', количество '
                . $inventory->quantity
                . ', склад'
                . $store->name
                .' от '
                .auth()->user()->email
        ]);
    }

    public function created(Inventory $inventory): void
    {
        $this->handle($inventory, 'created');
    }
    
    public function updated(Inventory $inventory): void
    {
        $this->handle($inventory, 'updated');
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

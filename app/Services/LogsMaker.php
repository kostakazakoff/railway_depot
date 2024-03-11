<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Log;
use App\Models\Store;

class LogsMaker
{
    public static function log($operation, $article)
    {
        $inventory = Inventory::whereArticleId($article->id)->first();

        $store = Store::find($inventory->store_id);

        Log::create([
            'user_id' => auth()->user()->id,
            $operation => $article->description
                . ' с инвентарен номер '
                . $article->inventory_number
                . ', цена '
                . $article->price
                . ' лв., количество '
                . $inventory->quantity
                . ' бр., склад '
                . $store->name
                . ' от '
                . auth()->user()->email
        ]);
    }
}

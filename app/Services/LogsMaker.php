<?php

namespace App\Services;
use App\Models\Log;

class LogsMaker
{
    public static function log($operation, $inventory, $article, $store)
    {
        Log::create([
            'user_id' => auth()->user()->id,
            $operation => $article->description . ' с инвентарен номер '
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

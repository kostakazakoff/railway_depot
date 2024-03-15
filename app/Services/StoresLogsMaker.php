<?php

namespace App\Services;

use App\Models\Log;

class StoresLogsMaker
{
    public static function log($operation, $store)
    {
        Log::create([
            'user_id' => auth()->user()->id,
            $operation => $store->name
            .' от '
            . auth()->user()->email
        ]);
    }
}
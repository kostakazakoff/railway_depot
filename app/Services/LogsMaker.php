<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Inventory;
use App\Models\Log;
use App\Models\Store;
use App\Models\User;


class LogsMaker
{

    public static function log($operation, $object)
    {
        $object instanceof Article &&
            self::createArticleLog($operation, $object);
        $object instanceof Store &&
            self::createStoreLog($operation, $object);
        $object instanceof User &&
            self::createUserLog($operation, $object);
    }


    private static function createArticleLog($operation, $article)
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
                . ', от '
                . auth()->user()->email
        ]);
    }

    private static function createStoreLog($operation, $store)
    {
        Log::create([
            'user_id' => auth()->user()->id,
            $operation => 'Склад №:'
                . $store->name
                . ', от '
                . auth()->user()->email
        ]);
    }

    private static function createUserLog($operation, $user)
    {
        $listOfStores = [];

        foreach ($user->stores as $store) {
            array_push($listOfStores, $store->name);
        }

        $userStores = join(', ', $listOfStores);

        $userHasStores = '';

        $userStores &&
        $userHasStores = ', отговорен за склад №';

        Log::create([
            'user_id' => auth()->user()->id,
            $operation => $user->email
                . ' - '
                . $user->role
                . $userHasStores
                . $userStores
                . ', от '
                . auth()->user()->email
        ]);
    }
}

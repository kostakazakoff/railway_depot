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
        if ($object instanceof Article) {
            $inventory = Inventory::whereArticleId($object->id)->first();

            $store = Store::find($inventory->store_id);

            Log::create([
                'user_id' => auth()->user()->id,
                $operation => $object->description
                    . ' с инвентарен номер '
                    . $object->inventory_number
                    . ', цена '
                    . $object->price
                    . ' лв., количество '
                    . $inventory->quantity
                    . ' бр., склад '
                    . $store->name
                    . ', от '
                    . auth()->user()->email
            ]);
        } else if ($object instanceof Store) {
            Log::create([
                'user_id' => auth()->user()->id,
                $operation => $object->name
                    . ', от '
                    . auth()->user()->email
            ]);
        } else if ($object instanceof User) {
            $listOfStores = [];

            foreach ($object->stores as $store) {
                array_push($listOfStores, $store->name);
            }

            $userStores = join(', ', $listOfStores);

            $userHasStores = '';
            $userStores && $userHasStores = ', отговорен за '; 

            Log::create([
                'user_id' => auth()->user()->id,
                $operation => $object->email
                    . ' - '
                    . $object->role
                    . $userHasStores
                    . $userStores
                    . ', от '
                    . auth()->user()->email
            ]);
        }
    }
}

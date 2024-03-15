<?php

namespace App\Listeners;

use App\Services\StoresLogsMaker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreCreateLog
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
        $store = $event->store;

        StoresLogsMaker::log($operation, $store);
    }
}

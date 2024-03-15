<?php

namespace App\Listeners;

use App\Services\LogsMaker;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateLog
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
        $object = $event->object;

        LogsMaker::log($operation, $object);
    }
}

<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserForPasswordReset
{
    public function __construct()
    {
        //
    }

    public function handle(object $event): void
    {
        $user = $event->user;
        dd($user);
    }
}

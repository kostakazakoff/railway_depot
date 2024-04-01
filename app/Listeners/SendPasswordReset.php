<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Password;

class SendPasswordReset implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(object $event): void
    {
        Password::sendResetLink($event->email);
    }
}

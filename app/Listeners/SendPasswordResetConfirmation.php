<?php

namespace App\Listeners;

use App\Mail\PasswordResetConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetConfirmation
{
    public function __construct()
    {
        //
    }

    public function handle(object $event): void
    {
        $user = $event->user;
        Mail::to($user)->send(new PasswordResetConfirmation());
    }
}

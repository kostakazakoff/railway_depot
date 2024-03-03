<?php

namespace App\Exceptions;

use Exception;

class AppExceptions extends Exception
{
    public static function notAstaff() {
        return new self(message: 'Unauthorized access! You are not a staff member.');
    }

    public static function notAdmin() {
        return new self(message: 'Unauthorized access! You are not an administrator.');
    }
}

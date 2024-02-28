<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public static function Role() {
        return new self(message: 'Unauthorized access! You have to be a staff member.');
    }
}

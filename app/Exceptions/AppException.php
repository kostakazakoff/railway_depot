<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AppException extends \Exception
{
    public static function notAdmin(): AppException
    {
        return new self(message: 'Неоторизиран достъп! Нужни са администраторски права', code: 401);
    }

    public static function unauthorized()
    {
        return new self(message: 'Нямате достъп. Свържете се с вашия администратор', code: 401);
    }

    public static function invalidCredentials(): AppException
    {
        return new self(message: 'Моля, въведете правилни имейл и парола (минимум 4 символа)!', code: 403);
    }

    public static function notFound(): AppException
    {
        return new self(message: 'Липсва в базата данни!', code: 404);
    }

    public static function articlesNotFound(): AppException
    {
        return new self(message: 'Не са открити артикули с подадените параметри!', code: 404);
    }

    public static function invalidPassword(): AppException
    {
        return new self(message: 'Грешна парола!', code: 401);
    }
}

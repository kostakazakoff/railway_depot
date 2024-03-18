<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AppException extends \Exception
{
    public static function notSuperuser(): AppException
    {
        return new self(message: 'Неоторизиран достъп! Нужни са права с най-високо ниво на достъп', code: 401);
    }


    public static function notAdmin(): AppException
    {
        return new self(message: 'Неоторизиран достъп! Нужни са администраторски права', code: 401);
    }

    public static function unauthorized()
    {
        return new self(message: 'Нямате достъп. Свържете се с вашия администратор', code: 401);
    }

    public static function unauthorizedForStore()
    {
        return new self(message: 'Нямате достъп до този склад. Свържете се с вашия администратор', code: 401);
    }

    public static function invalidCredentials(): AppException
    {
        return new self(message: 'Моля, въведете правилни имейл и парола (минимум 4 символа)!', code: 403);
    }

    public static function notFound($item): AppException
    {
        return new self(message: 'Няма '.$item.' в базата данни!', code: 404);
    }

    public static function invalidPassword(): AppException
    {
        return new self(message: 'Грешна парола!', code: 401);
    }

    public static function notActiveUser($user): AppException
    {
        return new self(message: 'Потребителят '.$user.' не е активен! Моля, назначете му роля.', code: 401);
    }

    public static function storeIsNotEmpty($store): AppException
    {
        return new self(message: 'Склад '.$store.' не е празен! За да изтриете склада, първо трябва да премахнете или преместите наличностите в друг склад.', code: 401);
    }
}

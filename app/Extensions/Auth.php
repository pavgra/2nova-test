<?php namespace App\Extensions;

use Symfony\Component\HttpFoundation\Session\Session;

class Auth
{
    private static $userIdField = 'user_id';

    public static function userId() {
        return (new Session())->get(static::$userIdField);
    }

    public static function logIn($userId) {
        (new Session())->set(static::$userIdField, $userId);
    }

    public static function logOut() {
        (new Session())->remove(static::$userIdField);
    }
}
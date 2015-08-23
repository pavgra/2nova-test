<?php
namespace App\Extensions;

use Symfony\Component\HttpFoundation\Session\Session;

class Auth
{
    private static $userIdField = 'user_id';

    /**
     * Retrieve logged in user identifier
     * @return integer
     */
    public static function userId()
    {
        return (new Session())->get(static::$userIdField);
    }

    /**
     * Log in user by identifier
     * @param integer $userId
     */
    public static function logIn($userId)
    {
        (new Session())->set(static::$userIdField, $userId);
    }

    /**
     * Log out current user
     */
    public static function logOut()
    {
        (new Session())->remove(static::$userIdField);
    }
}
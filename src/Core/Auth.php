<?php
declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function login(int $userId): void
    {
        Session::set('user_id', $userId);
        session_regenerate_id(true);
    }

    public static function logout(): void
    {
        Session::remove('user_id');
        session_regenerate_id(true);
    }
}

<?php
declare(strict_types=1);

namespace App\Core;

class Security
{
    public static function generateCsrfToken(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        if (!$token || !Session::has('csrf_token')) {
            return false;
        }
        return hash_equals(Session::get('csrf_token'), $token);
    }
}

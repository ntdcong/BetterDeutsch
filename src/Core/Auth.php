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

    public static function user(): array|false
    {
        $id = self::id();
        if (!$id) return false;
        $userModel = new \App\Models\User();
        return $userModel->findById($id);
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user && isset($user['role']) && $user['role'] === 'admin';
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

<?php
declare(strict_types=1);

namespace App\Core;

class Session
{
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }
}

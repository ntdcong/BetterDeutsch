<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = BASE_PATH . '/src/Views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("View {$view} không tồn tại.");
        }
    }

    protected function e(?string $string): string
    {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}

<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, array|callable $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute(string $method, string $path, array|callable $callback): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                if (is_callable($route['callback'])) {
                    call_user_func($route['callback']);
                } elseif (is_array($route['callback'])) {
                    [$class, $methodName] = $route['callback'];
                    if (class_exists($class)) {
                        $controller = new $class();
                        if (method_exists($controller, $methodName)) {
                            $controller->$methodName();
                        } else {
                            $this->abort(500, "Method {$methodName} không tồn tại trong Controller {$class}");
                        }
                    } else {
                        $this->abort(500, "Controller {$class} không tồn tại");
                    }
                }
                return;
            }
        }

        $this->abort();
    }

    private function abort(int $code = 404, string $message = "Page Not Found"): void
    {
        http_response_code($code);
        echo "<h1>{$code} - {$message}</h1>";
        exit;
    }
}

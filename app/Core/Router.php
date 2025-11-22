<?php

namespace App\Core;

// Router class for handling URL routing
class Router
{
    private $routes = [];

    public function addRoute($path, $controller, $method)
    {
        $this->routes[$path] = [
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function route($url)
    {
        // Check if route exists
        if (array_key_exists($url, $this->routes)) {
            $controllerName = 'App\\Controllers\\' . $this->routes[$url]['controller'];
            $methodName = $this->routes[$url]['method'];
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $methodName)) {
                    $controller->$methodName();
                } else {
                    $this->show404();
                }
            } else {
                $this->show404();
            }
        } else {
        // Default to dashboard if no route specified
            if ($url === '/' || $url === '') {
                $controller = new \App\Controllers\DashboardController();
                $controller->index();
            } else {
                $this->show404();
            }
        }
    }

    private function show404()
    {
        header("HTTP/1.0 404 Not Found");
// Use custom error handler if available
        if (class_exists('\App\Core\ErrorHandler')) {
            \App\Core\ErrorHandler::show404();
            exit();
        }

        // Use custom 404 page if available
        // if (file_exists(APP_ROOT . '/404.php')) {
        //     include APP_ROOT . '/404.php';
        //     exit();
        // }

        // Fallback to simple error message
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The page you are looking for does not exist.</p>";
        echo "<a href='" . (defined('APP_URL') ? APP_URL : '/') . "'>Go to Home</a>";
        exit();
    }
}

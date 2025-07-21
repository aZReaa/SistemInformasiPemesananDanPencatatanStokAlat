<?php

class Router
{
    private $routes = [];

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove hakikah base path and normalize
        $path = str_replace('/hakikah', '', $path);
        
        // Handle public folder access
        if (strpos($path, '/public') === 0) {
            $path = str_replace('/public', '', $path);
        }
        
        // Remove .php extension if present
        if (substr($path, -4) === '.php') {
            $path = substr($path, 0, -4);
        }
        
        if ($path === '' || $path === '/') {
            $path = '/';
        }

        // Debug logging (remove in production)
        error_log("Router: Method=$method, Path='$path'");
        
        // Check exact match first
        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            $this->executeCallback($callback);
            return;
        }

        // Check regex patterns
        foreach ($this->routes[$method] ?? [] as $pattern => $callback) {
            if (preg_match("#^{$pattern}$#", $path, $matches)) {
                array_shift($matches); // Remove full match
                
                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                } elseif (is_string($callback)) {
                    $parts = explode('@', $callback);
                    $controller = $parts[0];
                    $methodName = $parts[1];
                    
                    $controllerInstance = new $controller();
                    call_user_func_array([$controllerInstance, $methodName], $matches);
                }
                return;
            }
        }

        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Path: $path</p>";
        echo "<p>Method: $method</p>";
        echo "<p>Available routes for $method:</p><ul>";
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            echo "<li>$route</li>";
        }
        echo "</ul>";
        echo "<p><a href='/hakikah/'>← Back to home</a></p>";
    }

    private function executeCallback($callback)
    {
        if (is_callable($callback)) {
            call_user_func($callback);
        } elseif (is_string($callback)) {
            $parts = explode('@', $callback);
            $controller = $parts[0];
            $method = $parts[1];
            
            $controllerInstance = new $controller();
            $controllerInstance->$method();
        }
    }
}
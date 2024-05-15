<?php

class Router
{
    protected $routes = [];

    private function registerRoute($method, $uri, $controller)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
        ];
    }

    public function addGet($uri, $controller)
    {
        $this->registerRoute('GET', $uri, $controller);
    }

    public function addPost($uri, $controller)
    {
        $this->registerRoute('POST', $uri, $controller);
    }

    public function addPut($uri, $controller)
    {
        $this->registerRoute('PUT', $uri, $controller);
    }

    public function addDelete($uri, $controller)
    {
        $this->registerRoute('DELETE', $uri, $controller);
    }

    public function route($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                require basePath($route['controller']);
                return;
            }
        }

        http_response_code(404);
        loadView('error/404');
    }
}

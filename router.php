<?php

class Router {
    protected $routes = [];

    private function registerRoute($method, $uri, $controller) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];
    }
    
    /**
     * 添加一个 GET 路由
     *
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function addGet($uri, $controller) {
        $this->registerRoute('GET', $uri, $controller);
    }
    
    /**
     * 添加一个 POST 路由
     *
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function addPost($uri, $controller) {
        $this->registerRoute('POST', $uri, $controller);
    }
    public function addPut($uri, $controller) {
        $this->registerRoute('PUT', $uri, $controller);
    }
    
    // 添加一个 DELETE 路由
    /**
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function addDelete($uri, $controller) {
        $this->registerRoute('DELETE', $uri, $controller);
    }
    public function error($httpCode = 404){
        http_response_code($httpCode);
        loadView("error/{$httpCode}");
        exit;
    }
    // 根据 URI 和方法调用控制器
    public function route($uri, $method) {
        foreach($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                require basePath($route['controller']);
                return;
            }
        }
    
        $this->error();  // 默认传递404
    }
}
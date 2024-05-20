<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorise;

class Router
{
    protected $routes = [];

    /**
     * 注册一条路由规则
     *
     * @param string $method
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    private function registerRoute($method, $uri, $action, $middleware = [])
    {
        list($controller, $controllerMethod) = explode('@', $action);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware' => $middleware
        ];
    }

    /**
     * 添加一个 GET 路由
     *
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function addGet($uri, $controller, $middleware = [])
    {
        $this->registerRoute('GET', $uri, $controller, $middleware);
    }

    /**
     * 添加一个 POST 路由
     *
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function addPost($uri, $controller, $middleware = [])
    {
        $this->registerRoute('POST', $uri, $controller, $middleware);
    }

    /**
     * 添加一个 PUT 路由
     *
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function addPut($uri, $controller, $middleware = [])
    {
        $this->registerRoute('PUT', $uri, $controller, $middleware);
    }

    /**
     * 添加一个 DELETE 路由
     *
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function addDelete($uri, $controller, $middleware = [])
    {
        $this->registerRoute('DELETE', $uri, $controller, $middleware);
    }

    /**
     * 处理传入的请求 URI
     *
     * @param string $uri
     * @return void
     */
    public function route($uri)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        $uriSegments = explode('/', trim($uri, '/'));

        foreach ($this->routes as $route) {
            $routeSegments = explode('/', trim($route['uri'], '/'));
            $match = false;

            if (count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {
                $params = [];
                $match = true;

                for ($i = 0; $i < count($uriSegments); $i++) {
                    if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                        $match = false;
                        break;
                    }

                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }
            }

            if ($match) {
                foreach ($route['middleware'] as $middleware) {
                    (new Authorise())->handle($middleware);
                }
                $controller = 'App\\Controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];

                $controllerInstance = new $controller();
                $controllerInstance->$controllerMethod($params);
                return;
            }
        }

        ErrorController::notFound();
    }
}

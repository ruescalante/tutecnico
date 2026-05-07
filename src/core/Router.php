<?php

class Router
{
    private array $routes = [];
    private array $globalMiddleware = [];

    // Registrar rutas GET
    public function get(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    // Registrar rutas POST
    public function post(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function patch(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $action, $middleware);
    }

    public function delete(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $action, $middleware);
    }

    public function addMiddleware(string $middlewareClass): void
    {
        $this->globalMiddleware[] = $middlewareClass;
    }

    private function addRoute(string $method, string $path, string $action, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path'   => $path,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    // Despachar la petición actual
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $params = [];
            if ($this->matchPath($route['path'], $path, $params)) {
                [$controllerName, $methodName] = explode('@', $route['action']);

                require_once BASE_PATH . "/controllers/{$controllerName}.php";

                $controller = new $controllerName();

                // Build Request
                require_once BASE_PATH . '/core/Request.php';
                $request = new Request($_GET, $_POST, $_SERVER, $params);

                // Build middleware stack (global first, then route)
                $middlewareStack = array_merge($this->globalMiddleware, $route['middleware']);

                $runner = $this->createRunner($middlewareStack, function ($request) use ($controller, $methodName) {
                    // Call controller method with Request if it expects params
                    $ref = new ReflectionMethod($controller, $methodName);
                    if ($ref->getNumberOfParameters() > 0) {
                        return $controller->$methodName($request);
                    }
                    return $controller->$methodName();
                });

                $runner($request);
                return;
            }
        }

        // Ninguna ruta coincidió → 404
        http_response_code(404);
        require_once BASE_PATH . '/controllers/ErrorController.php';
        (new ErrorController())->notFound();
    }

    private function matchPath(string $routePath, string $requestPath, array &$params): bool
    {
        $routePath = trim($routePath, '/');
        $requestPath = trim($requestPath, '/');

        $routeSegments = $routePath === '' ? [] : explode('/', $routePath);
        $pattern = '';

        foreach ($routeSegments as $seg) {
            if (strlen($seg) && $seg[0] === ':') {
                // parameter
                // syntax :name or :name(type) or :name?
                if (preg_match('/^:([a-zA-Z_][\w]*)(?:\(([^)]+)\))?(\?)?$/', $seg, $m)) {
                    $name = $m[1];
                    $type = $m[2] ?? null;
                    $optional = isset($m[3]) && $m[3] === '?';

                    $subPattern = $type === 'int' ? '(?P<' . $name . '>\d+)' : '(?P<' . $name . '>[^/]+)';

                    if ($optional) {
                        $pattern .= '(?:/' . $subPattern . ')?';
                    } else {
                        $pattern .= '/' . $subPattern;
                    }
                    continue;
                }
            }

            $pattern .= '/' . preg_quote($seg, '#');
        }

        $pattern = '#^' . ($pattern === '' ? '/' : $pattern) . '$#';

        if (preg_match($pattern, '/' . $requestPath, $matches)) {
            // extract named params
            foreach ($matches as $k => $v) {
                if (!is_int($k)) {
                    $params[$k] = $v;
                }
            }
            return true;
        }

        return false;
    }

    private function createRunner(array $middlewareStack, callable $final): callable
    {
        $runner = array_reduce(array_reverse($middlewareStack), function ($next, $middleware) {
            return function ($request) use ($middleware, $next) {
                // middleware can be class name or callable
                if (is_string($middleware) && class_exists($middleware)) {
                    $mw = new $middleware();
                    return $mw->handle($request, $next);
                }

                if (is_callable($middleware)) {
                    return $middleware($request, $next);
                }

                throw new RuntimeException('Invalid middleware');
            };
        }, $final);

        return $runner;
    }
}


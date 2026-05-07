<?php

class Router
{
    private array $routes = [];

    // Registrar rutas GET
    public function get(string $path, string $action): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'path'   => $path,
            'action' => $action
        ];
    }

    // Registrar rutas POST
    public function post(string $path, string $action): void
    {
        $this->routes[] = [
            'method' => 'POST',
            'path'   => $path,
            'action' => $action
        ];
    }

    // Despachar la petición actual
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                // Separar "SolicitudController@index"
                [$controllerName, $methodName] = explode('@', $route['action']);

                require_once BASE_PATH . "/controllers/{$controllerName}.php";

                $controller = new $controllerName();
                $controller->$methodName();
                return;
            }
        }

        // Ninguna ruta coincidió → 404
        http_response_code(404);
        require_once BASE_PATH . '/controllers/ErrorController.php';
        (new ErrorController())->notFound();
    }
}

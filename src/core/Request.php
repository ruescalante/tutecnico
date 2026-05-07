<?php
class Request
{
    public string $method;
    public string $path;
    public array $get;
    public array $post;
    public array $server;
    public array $params = [];
    public array $headers = [];

    public function __construct(array $get = [], array $post = [], array $server = [], array $params = [])
    {
        $this->method = $server['REQUEST_METHOD'] ?? 'GET';
        $this->path   = parse_url($server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $this->get    = $get;
        $this->post   = $post;
        $this->server = $server;
        $this->params = $params;
        $this->headers = $this->parseHeaders();
    }

    protected function parseHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function has(string $key): bool
    {
        return isset($this->post[$key]) || isset($this->get[$key]);
    }

    public function json(): ?array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : null;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function param(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function isMethod(string $m): bool
    {
        return strtoupper($this->method) === strtoupper($m);
    }

    public function isAjax(): bool
    {
        return (isset($this->headers['X-Requested-With']) && $this->headers['X-Requested-With'] === 'XMLHttpRequest');
    }
}

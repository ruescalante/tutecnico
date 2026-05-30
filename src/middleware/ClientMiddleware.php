<?php

require_once BASE_PATH . '/core/Request.php';
require_once BASE_PATH . '/middleware/RoleMiddleware.php';

class ClientMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        RoleMiddleware::ensure('cliente');
        return $next($request);
    }
}

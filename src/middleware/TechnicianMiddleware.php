<?php

require_once BASE_PATH . '/core/Request.php';
require_once BASE_PATH . '/middleware/RoleMiddleware.php';

class TechnicianMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        RoleMiddleware::ensure('tecnico');
        return $next($request);
    }
}

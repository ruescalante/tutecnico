<?php

require_once BASE_PATH . '/core/Request.php';
require_once BASE_PATH . '/middleware/RoleMiddleware.php';

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        RoleMiddleware::ensure('admin');
        return $next($request);
    }
}

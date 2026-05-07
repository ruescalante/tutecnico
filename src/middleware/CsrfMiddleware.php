<?php
require_once BASE_PATH . '/core/Request.php';
require_once BASE_PATH . '/exceptions/ValidationException.php';

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $method = strtoupper($request->method);
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $tokenSession = $_SESSION['csrf_token'] ?? null;
            $tokenInput = $request->input('_csrf') ?? null;
            if (!$tokenSession || !$tokenInput || !hash_equals($tokenSession, $tokenInput)) {
                throw new ValidationException(['csrf' => 'Token CSRF inválido']);
            }
        }

        return $next($request);
    }
}

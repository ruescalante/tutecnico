<?php
require_once BASE_PATH . '/core/Request.php';

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            if ($request->isAjax()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            header('Location: /login');
            exit;
        }

        return $next($request);
    }
}

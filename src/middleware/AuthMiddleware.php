<?php
require_once BASE_PATH . '/core/Request.php';

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            // If AJAX, return 401 JSON
            if ($request->isAjax()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            // Otherwise redirect to login placeholder
            header('Location: /login');
            exit;
        }

        // user present — continue
        return $next($request);
    }
}

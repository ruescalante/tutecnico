<?php
require_once BASE_PATH . '/core/Request.php';
require_once BASE_PATH . '/models/User.php';

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $userId = $_SESSION['user_id'] ?? null;
        $path = $request->path ?? '/';

        if ($userId && ($path === '/login' || $path === '/registro')) {
            header('Location: /dashboard');
            exit;
        }

        if (!$userId) {
            if ($request->isAjax()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            $_SESSION['intended_url'] = $path;
            header('Location: /login');
            exit;
        }

        $dbUser = User::findById((int) $userId);
        if (!$dbUser || (int) ($dbUser['activo'] ?? 0) !== 1) {
            unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['user_name'], $_SESSION['intended_url']);
            $_SESSION['errors'] = ['auth' => ['Tu sesión ha expirado o tu cuenta está inactiva']];
            header('Location: /login');
            exit;
        }

        $_SESSION['role'] = $dbUser['rol'];
        $_SESSION['user_name'] = $dbUser['nombre'];
        $_SESSION['user_photo'] = $dbUser['foto_perfil'];

        return $next($request);
    }
}

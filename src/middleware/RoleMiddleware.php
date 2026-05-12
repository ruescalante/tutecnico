<?php

require_once BASE_PATH . '/core/Request.php';

class RoleMiddleware
{
    public static function ensure(string $expectedRole): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $role = $_SESSION['role'] ?? null;
        if ($role !== $expectedRole) {
            http_response_code(403);
            $_SESSION['errors'] = ['auth' => ['No tienes permisos para acceder a esta sección']];
            header('Location: /dashboard');
            exit;
        }
    }
}

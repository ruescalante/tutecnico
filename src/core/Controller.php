<?php
class Controller
{
    protected function render(string $view, array $data = []): void
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        $success = $_SESSION['success'] ?? null;

        unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);

        extract($data);
        ob_start();
        require_once BASE_PATH . "/views/{$view}.php";
        $content = ob_get_clean();

        require_once BASE_PATH . '/views/layouts/app.php';
    }
}
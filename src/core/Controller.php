<?php
class Controller
{
    protected function render(string $view, array $data = []): void
    {
        // las keys del array se convierten en variables
        extract($data);

        // captura el HTML de la vista en lugar de imprimirlo
        ob_start();
        require_once BASE_PATH . "/views/{$view}.php";
        $content = ob_get_clean();

        // renderiza el layout con $content adentro
        require_once BASE_PATH . '/views/layouts/app.php';
    }
}
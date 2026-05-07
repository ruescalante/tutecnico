<?php
require_once BASE_PATH . '/core/Controller.php';
class ErrorController extends Controller
{
    public function notFound(): void
    {
        $this->render('errors/404', [
            'pageTitle' => 'Página no encontrada',
        ]);
    }

    public function serverError(): void
    {
        $this->render('errors/500', [
            'pageTitle' => 'Error del servidor',
        ]);
    }
}
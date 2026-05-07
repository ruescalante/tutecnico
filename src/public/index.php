<?php
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Request.php';
require_once BASE_PATH . '/core/ValidationException.php';
require_once BASE_PATH . '/validators/Validator.php';
require_once BASE_PATH . '/helpers/sanitize.php';
require_once BASE_PATH . '/core/MiddlewareInterface.php';

$router = new Router();

// iniciar session para autentificacion cuando lo agregemos.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once BASE_PATH . '/routes/web.php';  // registra las rutas

$request = new Request($_GET, $_POST, $_SERVER);

try {
    $router->dispatch();
} catch (ValidationException $e) {
    if ($request->isAjax()) {
        http_response_code(422);
        header('Content-Type: application/json');
        echo json_encode(['errors' => $e->getErrors()]);
        exit;
    }

    $_SESSION['errors'] = $e->getErrors();
    $_SESSION['old'] = $_POST;

    $back = $_POST['_back_url'] ?? $_SERVER['HTTP_REFERER'] ?? '/';
    if (!is_string($back) || !str_starts_with($back, '/')) {
        $back = '/';
    }
    header('Location: ' . $back);
    exit;
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    require_once BASE_PATH . '/controllers/ErrorController.php';
    (new ErrorController())->serverError();
}

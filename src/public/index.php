<?php
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Request.php';
require_once BASE_PATH . '/exceptions/ValidationException.php';
require_once BASE_PATH . '/validators/Validator.php';   
require_once BASE_PATH . '/helpers/sanitize.php';
require_once BASE_PATH . '/core/MiddlewareInterface.php';

$router = new Router();

// start session for CSRF and auth later
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once BASE_PATH . '/routes/web.php';  // registra las rutas

$request = new Request($_GET, $_POST, $_SERVER);

try {
	$router->dispatch();  // atiende la petición
} catch (ValidationException $e) {
	http_response_code(422);
	if ($request->isAjax()) {
		header('Content-Type: application/json');
		echo json_encode(['errors' => $e->getErrors()]);
	} else {
		echo '<h1>Errores de validación</h1><ul>';
		foreach ($e->getErrors() as $field => $msgs) {
			foreach ($msgs as $m) {
				echo '<li>' . htmlspecialchars($field) . ': ' . htmlspecialchars($m) . '</li>';
			}
		}
		echo '</ul>';
	}
	exit;
} catch (Throwable $e) {
	// unexpected error — log and show 500
	error_log($e->getMessage());
	http_response_code(500);
	require_once BASE_PATH . '/controllers/ErrorController.php';
	(new ErrorController())->serverError();
}
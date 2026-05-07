<?php
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/core/Model.php';

$router = new Router();

require_once BASE_PATH . '/routes/web.php';  // registra las rutas

$router->dispatch();  // atiende la petición
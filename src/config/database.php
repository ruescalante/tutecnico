<?php
// =============================================================
// config/database.php
// Configuración de conexión a la base de datos (PDO)
// =============================================================

define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')    ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: 'tutecnico');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

class Database
{
    private static ?PDO $instance = null;

    /**
     * Devuelve la instancia única de PDO (Singleton).
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log('DB Connection error: ' . $e->getMessage());
                http_response_code(500);
                die(json_encode(['error' => 'Error de conexión a la base de datos.']));
            }
        }

        return self::$instance;
    }
    private function __construct() {}
    private function __clone()    {}
}
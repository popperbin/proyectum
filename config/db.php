<?php
/**
 * Configuración de la base de datos
 * Sistema de Gestión de Proyectos - Proyectum
 */

class Database {
    private $host = 'localhost';
    private $port = '3306';
    private $dbname = 'proyectum';  // 👈 nombre de la BD que importaste en PhpMyAdmin
    private $username = 'root';     // 👈 en XAMPP es root por defecto
    private $password = '';         // 👈 en XAMPP root no tiene contraseña por defecto
    private $charset = 'utf8mb4';
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
    }

    /**
     * Singleton para obtener instancia única
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            echo "<pre>";
            echo "Error PDO " . $e->getMessage();
            echo "SQL " . $sql . "\n";
            echo "params " . print_r($params, true);
            echo "</pre>";
            exit();
        }
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert($sql, $params = []) {
        try {
            // Debug: imprimir SQL y parámetros
            error_log("SQL a ejecutar: " . $sql);
            error_log("Parámetros: " . print_r($params, true));

            $this->query($sql, $params);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("ERROR INSERT: " . $e->getMessage());
            throw $e;
        }
    }


    public function execute($sql, $params = []) {
    try {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Error en ejecución SQL: " . $e->getMessage());
        error_log("SQL: " . $sql);
        error_log("Params: " . print_r($params, true));
        throw new Exception("Error al ejecutar la consulta en la base de datos");
    }
}


}

// Helper global
function getDB() {
    return Database::getInstance();
}

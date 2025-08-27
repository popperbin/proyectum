<?php
/**
 * Configuración de la base de datos
 * Sistema de Gestión de Proyectos
 */

class Database {
    private $host = 'localhost';
    private $dbname = 'proyectum';
    private $username = 'root';
    private $password = '123456';
    private $charset = 'utf8mb4';
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
        ];

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
    }

    /**
     * Singleton pattern para obtener la instancia única de la base de datos
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener la conexión PDO
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Ejecutar una consulta preparada
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error en consulta SQL: " . $e->getMessage());
            throw new Exception("Error en la consulta a la base de datos");
        }
    }

    /**
     * Obtener un solo registro
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Obtener múltiples registros
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insertar un registro y obtener el ID generado
     */
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->pdo->lastInsertId();
    }

    /**
     * Ejecutar una actualización o eliminación
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Revertir transacción
     */
    public function rollback() {
        return $this->pdo->rollback();
    }

    /**
     * Verificar si estamos en una transacción
     */
    public function inTransaction() {
        return $this->pdo->inTransaction();
    }

    /**
     * Obtener información sobre la base de datos
     */
    public function getDatabaseInfo() {
        return [
            'server_version' => $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
            'client_version' => $this->pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
            'connection_status' => $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
        ];
    }
}

// Función helper para obtener la conexión de forma rápida
function getDB() {
    return Database::getInstance();
}

// Script de creación de tablas (ejecutar solo una vez)
function crearEstructuraBD() {
    $db = Database::getInstance();
    
    $tablas = [
        // Tabla usuarios
        "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombres VARCHAR(100) NOT NULL,
            apellidos VARCHAR(100) NOT NULL,
            cedula VARCHAR(20) UNIQUE NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            direccion TEXT,
            celular VARCHAR(15),
            cargo VARCHAR(100),
            rol ENUM('administrador', 'gestor', 'colaborador', 'cliente') NOT NULL,
            estado ENUM('activo', 'inactivo') DEFAULT 'activo',
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",

        // Tabla proyectos
        "CREATE TABLE IF NOT EXISTS proyectos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(200) NOT NULL,
            descripcion TEXT,
            fecha_inicio DATE,
            fecha_fin DATE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            estado ENUM('planificacion', 'activo', 'en_pausa', 'completado', 'cancelado') DEFAULT 'planificacion',
            gestor_id INT,
            cliente_id INT,
            presupuesto DECIMAL(12,2),
            FOREIGN KEY (gestor_id) REFERENCES usuarios(id),
            FOREIGN KEY (cliente_id) REFERENCES usuarios(id)
        )",

        // Tabla listas (para organizar tareas tipo Kanban)
        "CREATE TABLE IF NOT EXISTS listas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            proyecto_id INT NOT NULL,
            orden INT DEFAULT 0,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE
        )",

        // Tabla tareas
        "CREATE TABLE IF NOT EXISTS tareas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(200) NOT NULL,
            descripcion TEXT,
            proyecto_id INT NOT NULL,
            lista_id INT,
            asignado_a INT,
            fecha_inicio DATE,
            fecha_fin DATE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            estado ENUM('pendiente', 'en_progreso', 'completado', 'cancelado') DEFAULT 'pendiente',
            prioridad ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
            estimacion_horas INT,
            horas_trabajadas INT DEFAULT 0,
            FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
            FOREIGN KEY (lista_id) REFERENCES listas(id) ON DELETE SET NULL,
            FOREIGN KEY (asignado_a) REFERENCES usuarios(id)
        )",

        // Tabla comentarios (para tareas)
        "CREATE TABLE IF NOT EXISTS comentarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tarea_id INT NOT NULL,
            usuario_id INT NOT NULL,
            comentario TEXT NOT NULL,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tarea_id) REFERENCES tareas(id) ON DELETE CASCADE,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        )",

        // Tabla riesgos
        "CREATE TABLE IF NOT EXISTS riesgos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            proyecto_id INT NOT NULL,
            descripcion TEXT NOT NULL,
            impacto ENUM('bajo', 'medio', 'alto', 'critico') NOT NULL,
            probabilidad ENUM('baja', 'media', 'alta') NOT NULL,
            medidas_mitigacion TEXT,
            estado ENUM('identificado', 'en_seguimiento', 'mitigado', 'ocurrido') DEFAULT 'identificado',
            fecha_identificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            responsable_id INT,
            FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
            FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
        )",

        // Tabla informes
        "CREATE TABLE IF NOT EXISTS informes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            proyecto_id INT NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            contenido TEXT,
            tipo ENUM('progreso', 'final', 'riesgos', 'personalizado') DEFAULT 'progreso',
            archivo_pdf VARCHAR(255),
            generado_por INT NOT NULL,
            fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
            FOREIGN KEY (generado_por) REFERENCES usuarios(id)
        )",

        // Tabla log de auditoría
        "CREATE TABLE IF NOT EXISTS log_auditoria (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT,
            accion VARCHAR(100) NOT NULL,
            tabla_afectada VARCHAR(50),
            registro_id INT,
            detalles TEXT,
            ip_address VARCHAR(45),
            fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        )",

        // Tabla recursos (para gestión de recursos)
        "CREATE TABLE IF NOT EXISTS recursos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            tipo ENUM('humano', 'material', 'equipamiento', 'software') NOT NULL,
            descripcion TEXT,
            costo_hora DECIMAL(10,2),
            disponible BOOLEAN DEFAULT TRUE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // Tabla asignación de recursos a tareas
        "CREATE TABLE IF NOT EXISTS tarea_recursos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tarea_id INT NOT NULL,
            recurso_id INT NOT NULL,
            cantidad INT DEFAULT 1,
            fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tarea_id) REFERENCES tareas(id) ON DELETE CASCADE,
            FOREIGN KEY (recurso_id) REFERENCES recursos(id)
        )"
    ];

    try {
        foreach ($tablas as $sql) {
            $db->execute($sql);
        }
        
        // Crear usuario administrador por defecto
        $admin_exists = $db->fetchOne("SELECT id FROM usuarios WHERE email = 'admin@proyectum.com'");
        if (!$admin_exists) {
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $db->execute(
                "INSERT INTO usuarios (nombres, apellidos, cedula, email, password, rol) VALUES (?, ?, ?, ?, ?, ?)",
                ['Administrador', 'Sistema', '00000000', 'admin@proyectum.com', $password_hash, 'administrador']
            );
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Error creando estructura BD: " . $e->getMessage());
        return false;
    }
}
?>
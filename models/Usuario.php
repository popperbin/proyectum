<?php
require_once __DIR__ . "/../config/db.php";

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Login del usuario
     */
    public function login($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
        $usuario = $this->db->fetchOne($sql, [$email]);


        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }

    /**
     * Registrar usuario nuevo
     */
    public function registrar($nombres, $apellidos, $cedula, $email, $password, $rol = "cliente") {
        // Encriptar la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombres, apellidos, cedula, email, password, rol) 
                VALUES (?, ?, ?, ?, ?, ?)";
        try {
            return $this->db->insert($sql, [$nombres, $apellidos, $cedula, $email, $hash, $rol]);
        } catch (Exception $e) {
            error_log("Error al registrar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    /** 
     * Obtener todos los colaboradores
     */
    public function obtenerColaboradores() {
        $sql = "SELECT id, nombres, apellidos 
            FROM usuarios 
                WHERE TRIM(rol) = 'colaborador' 
                    AND TRIM(estado) = 'activo'";
    
        $result = $this->db->fetchAll($sql);

        echo "<pre>DEBUG obtenerColaboradores:\n";
        var_dump($result);
        echo "</pre>";

        return $result;
    }


    /**
     * Listar todos los usuarios
     */
    public function listar() {
        $sql = "SELECT id, nombres, apellidos, email, rol, estado, fecha_creacion 
                FROM usuarios ORDER BY fecha_creacion DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id, $data) {
        $sql = "UPDATE usuarios 
                SET nombres = ?, apellidos = ?, cedula = ?, email = ?, direccion = ?, celular = ?, cargo = ?, rol = ?, estado = ? 
                WHERE id = ?";
        return $this->db->execute($sql, [
            $data['nombres'], $data['apellidos'], $data['cedula'],
            $data['email'], $data['direccion'], $data['celular'],
            $data['cargo'], $data['rol'], $data['estado'], $id
        ]);
    }

    /**
     * Eliminar usuario
     */
    public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}

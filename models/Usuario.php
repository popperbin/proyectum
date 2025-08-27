<?php
/**
 * Modelo Usuario
 * Maneja todas las operaciones relacionadas con usuarios del sistema
 */

require_once __DIR__ . "/../config/db.php";

class Usuario {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function registrar($nombres, $apellidos, $cedula, $email, $password, $rol = "cliente") {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombres, apellidos, cedula, email, password, rol) 
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->db->execute($sql, [$nombres, $apellidos, $cedula, $email, $hash, $rol]);
    }

    public function login($email, $password) {
        $usuario = $this->db->fetchOne("SELECT * FROM usuarios WHERE email = ? AND estado = 'activo'", [$email]);
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }

    public function obtenerPorId($id) {
        return $this->db->fetchOne("SELECT id, nombres, apellidos, email, rol, estado 
                                    FROM usuarios WHERE id = ?", [$id]);
    }
}

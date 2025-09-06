<?php
require_once __DIR__ . "/../config/db.php";

class Comentario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Obtener comentarios de una tarea
    public function listarPorTarea($tarea_id) {
        return $this->db->fetchAll(
            "SELECT c.*, CONCAT(u.nombres, ' ', u.apellidos) AS autor
             FROM comentarios c
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             WHERE c.tarea_id = ?
             ORDER BY c.fecha_creacion ASC",
            [$tarea_id]
        );
    }

    // Crear comentario
    public function crear($tarea_id, $usuario_id, $comentario) {
        $sql = "INSERT INTO comentarios (tarea_id, usuario_id, comentario, fecha_creacion)
                VALUES (?, ?, ?, NOW())";
        return $this->db->insert($sql, [$tarea_id, $usuario_id, $comentario]);
    }

    // Eliminar comentario (opcional)
    public function eliminar($id) {
        return $this->db->execute("DELETE FROM comentarios WHERE id = ?", [$id]);
    }
}

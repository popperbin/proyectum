<?php
require_once __DIR__ . "/../config/db.php";

class Tarea {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance(); // ✅ Usamos Singleton
    }

    public function listarPorProyecto($proyecto_id) {
        $sql = "SELECT t.*, u.nombres as responsable 
                FROM tareas t 
                JOIN usuarios u ON t.responsable_id = u.id 
                WHERE proyecto_id = ?";
        return $this->db->fetchAll($sql, [$proyecto_id]);
    }

    public function crear($data) {
        $sql = "INSERT INTO tareas 
                (proyecto_id, titulo, descripcion, responsable_id, fecha_inicio, fecha_fin, estado, prioridad)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->insert($sql, [
            $data['proyecto_id'],
            $data['titulo'],
            $data['descripcion'],
            $data['responsable_id'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['estado'],
            $data['prioridad']
        ]);
    }

    public function actualizarEstado($id, $estado) {
        $sql = "UPDATE tareas SET estado=? WHERE id=?";
        return $this->db->execute($sql, [$estado, $id]);
    }
}

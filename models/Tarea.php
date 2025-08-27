<?php
require_once __DIR__ . "/../config/db.php";

class Tarea {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function listarPorProyecto($proyecto_id) {
        $stmt = $this->conn->prepare("SELECT t.*, u.nombres as responsable 
                                      FROM tareas t 
                                      JOIN usuarios u ON t.responsable_id = u.id 
                                      WHERE proyecto_id = ?");
        $stmt->execute([$proyecto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO tareas (proyecto_id, titulo, descripcion, responsable_id, fecha_inicio, fecha_fin, estado, prioridad)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
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
        $stmt = $this->conn->prepare("UPDATE tareas SET estado=? WHERE id=?");
        return $stmt->execute([$estado, $id]);
    }
}

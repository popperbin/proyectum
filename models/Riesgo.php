<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Usuario.php';


class Riesgo {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listarPorProyecto($proyecto_id) {
        $sql = "SELECT r.*, u.nombres, u.apellidos 
                FROM riesgos r
                LEFT JOIN usuarios u ON r.responsable_id = u.id
                WHERE r.proyecto_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$proyecto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear riesgo
    public function crear($data) {
        $sql = "INSERT INTO riesgos 
            (proyecto_id, descripcion, impacto, probabilidad, medidas_mitigacion, estado, fecha_identificacion, responsable_id) 
            VALUES (?, ?, ?, ?, ?, 'pendiente', NOW(), ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['proyecto_id'],
            $data['descripcion'],
            $data['impacto'],
            $data['probabilidad'],
            $data['medidas_mitigacion'] ?? null,
            $data['responsable_id'] ?? null
        ]);
    }

    // Obtener un riesgo
    public function obtener($id) {
        $stmt = $this->db->prepare("SELECT * FROM riesgos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar riesgo
    public function actualizar($id, $data) {
        $sql = "UPDATE riesgos 
                SET descripcion=?, impacto=?, probabilidad=?, medidas_mitigacion=?, estado=?, fecha_actualizacion=NOW()
                WHERE id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['descripcion'],
            $data['impacto'],
            $data['probabilidad'],
            $data['medidas_mitigacion'],
            $data['estado'],
            $id
        ]);
    }

    // Eliminar riesgo
    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM riesgos WHERE id=?");
        return $stmt->execute([$id]);
    }
}

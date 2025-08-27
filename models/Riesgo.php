<?php
require_once __DIR__ . '/../config/conexion.php';

class Riesgo {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    public function listarPorProyecto($idProyecto) {
        $stmt = $this->db->prepare("SELECT * FROM riesgos WHERE id_proyecto = ?");
        $stmt->execute([$idProyecto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data) {
        $stmt = $this->db->prepare("INSERT INTO riesgos (id_proyecto, descripcion, impacto, probabilidad, mitigacion) 
                                    VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['id_proyecto'],
            $data['descripcion'],
            $data['impacto'],
            $data['probabilidad'],
            $data['mitigacion']
        ]);
    }

    public function obtener($id) {
        $stmt = $this->db->prepare("SELECT * FROM riesgos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $data) {
        $stmt = $this->db->prepare("UPDATE riesgos SET descripcion=?, impacto=?, probabilidad=?, mitigacion=? WHERE id=?");
        return $stmt->execute([
            $data['descripcion'],
            $data['impacto'],
            $data['probabilidad'],
            $data['mitigacion'],
            $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM riesgos WHERE id=?");
        return $stmt->execute([$id]);
    }
}

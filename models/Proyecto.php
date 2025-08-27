<?php
require_once __DIR__ . "/../config/db.php";

class Proyecto {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function listarTodos() {
        return $this->db->fetchAll("SELECT p.*, u.nombres as gestor, c.nombres as cliente 
                                    FROM proyectos p
                                    LEFT JOIN usuarios u ON p.gestor_id = u.id
                                    LEFT JOIN usuarios c ON p.cliente_id = c.id");
    }

    public function listarPorCliente($clienteId) {
        return $this->db->fetchAll("SELECT * FROM proyectos WHERE cliente_id = ?", [$clienteId]);
    }

    public function obtenerPorId($id) {
        return $this->db->fetchOne("SELECT * FROM proyectos WHERE id = ?", [$id]);
    }

    public function crear($data) {
        $sql = "INSERT INTO proyectos (nombre, descripcion, fecha_inicio, fecha_fin, gestor_id, cliente_id, presupuesto) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->db->execute($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['gestor_id'],
            $data['cliente_id'],
            $data['presupuesto']
        ]);
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE proyectos SET nombre=?, descripcion=?, fecha_inicio=?, fecha_fin=?, gestor_id=?, cliente_id=?, presupuesto=?, estado=? 
                WHERE id=?";
        return $this->db->execute($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['gestor_id'],
            $data['cliente_id'],
            $data['presupuesto'],
            $data['estado'],
            $id
        ]);
    }

    public function eliminar($id) {
        return $this->db->execute("DELETE FROM proyectos WHERE id = ?", [$id]);
    }
}

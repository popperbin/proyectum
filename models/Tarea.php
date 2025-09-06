<?php
require_once __DIR__ . "/../config/db.php";

class Tarea {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Tareas de una lista
    public function listarPorLista($lista_id) {
        return $this->db->fetchAll(
            "SELECT t.*, CONCAT(u.nombres, ' ', u.apellidos) AS asignado_nombre
            FROM tareas t
            LEFT JOIN usuarios u ON t.asignado_a = u.id
            WHERE t.lista_id = ?
            ORDER BY t.id ASC",
    [$lista_id]
        );
        
    }
    public function obtenerPorId($id) {
        return $this->db->fetchAll(
            "SELECT t.*, CONCAT(u.nombres, ' ', u.apellidos) AS asignado_nombre
            FROM tareas t
            LEFT JOIN usuarios u ON t.asignado_a = u.id
            WHERE t.id = ?
            LIMIT 1",
            [$id]
        )[0] ?? null;
    }   
    // Crear tarea (incluye proyecto_id y lista_id)
    public function crear($data) {
        $sql = "INSERT INTO tareas 
            (nombre, descripcion, proyecto_id, lista_id, asignado_a, fecha_inicio, fecha_fin, estado, prioridad, fecha_creacion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        return $this->db->insert($sql, [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['proyecto_id'],
            $data['lista_id'],
            !empty($data['asignado_a']) ? $data['asignado_a'] : null,
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null,
            $data['estado'] ?? 'pendiente',
            $data['prioridad'] ?? 'media'
        ]);
    }


    // Editar tarea
    public function editar($id, $data) {
        $sql = "UPDATE tareas
                SET nombre = ?, descripcion = ?, asignado_a = ?, fecha_inicio = ?, fecha_fin = ?, estado = ?, lista_id = ?, prioridad = ?
                WHERE id = ?";

        return $this->db->execute($sql, [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['asignado_a'] ?? null,
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null,
            $data['estado'] ?? 'pendiente',
            $data['lista_id'] ?? null,
            $data['prioridad'] ?? 'media',
            $id
        ]);
    }

    // Mover tarea entre listas (drag & drop)
    public function mover($id, $nueva_lista_id) {
        $sql = "UPDATE tareas SET lista_id = ? WHERE id = ?";
        return $this->db->execute($sql, [$nueva_lista_id, $id]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM tareas WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}

<?php
require_once __DIR__ . "/../config/db.php";

class Proyecto {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function listarTodos() {
        return $this->db->fetchAll("SELECT p.*, u.nombres as gestor, c.nombres as cliente 
                                    FROM proyectos p
                                    LEFT JOIN usuarios u ON p.gestor_id = u.id
                                    LEFT JOIN usuarios c ON p.cliente_id = c.id
                                    ORDER BY p.fecha_creacion DESC");
                                    
    }

    public function listarPorGestor($gestorId) {
        return $this->db->fetchAll("SELECT * FROM proyectos WHERE gestor_id = ?", [$gestorId]);
    }

    public function obtenerPorId($id) {
        return $this->db->fetchOne("SELECT * FROM proyectos WHERE id = ?", [$id]);
    }

    public function crear($data) {
        $sql = "INSERT INTO proyectos (nombre, descripcion, fecha_inicio, fecha_fin, estado, gestor_id, cliente_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        return $this->db->insert($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['estado'] ?? 'activo',
            $data['gestor_id'],
            !empty($data['cliente_id']) ? $data['cliente_id'] : null,
        ]);

        if ($proyecto_id) {
            $this->crearListasBasicas($proyecto_id);
        }
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE proyectos 
                SET nombre=?, descripcion=?, fecha_inicio=?, fecha_fin=?, gestor_id=?, cliente_id=?, estado=? 
                WHERE id=?";
        return $this->db->execute($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['gestor_id'],
            $data['cliente_id'],
            $data['estado'],
            $id
        ]);
    }

    public function eliminar($id) {
        return $this->db->execute("DELETE FROM proyectos WHERE id = ?", [$id]);
    }

    private function crearListasBasicas($proyecto_id) {
        $listas = [
            ['nombre' => 'Por hacer','orden'=> '1'],
            ['nombre' => 'En progreso','orden'=> '2'],
            ['nombre' => 'Completado','orden'=> '3'],
        ];

        foreach ($listas as $lista) {
            $sql = "INSERT INTO listas (nombre, proyecto_id, orden) VALUES (?, ?, ?)";
            $this->db->insert($sql, [$lista['nombre'], $proyecto_id, $lista['orden']]);
        }
    }

    public function obtenerColaboradores($proyecto_id) {
        $sql = "SELECT u.* FROM usuarios u
                INNER JOIN proyecto_colaboradores pc ON u.id = pc.usuario_id
                WHERE pc.proyecto_id = ?";
        return $this->db->fetchAll($sql, [$proyecto_id]);
    }

    public function asignarColaborador($proyecto_id, $usuario_id) {
        $sql = "INSERT IGNORE INTO proyecto_colaboradores (proyecto_id, usuario_id) VALUE (?, ?)";
        return $this->db->execute($sql, [$proyecto_id, $usuario_id]);
    }

    public function listarPorCliente($clienteId) {
    // Trae solo los proyectos donde el cliente sea $clienteId
        return $this->db->fetchAll(
            "SELECT p.*, u.nombres as gestor, c.nombres as cliente
            FROM proyectos p
            LEFT JOIN usuarios u ON p.gestor_id = u.id
            LEFT JOIN usuarios c ON p.cliente_id = c.id
            WHERE p.cliente_id = ? 
            ORDER BY p.fecha_creacion DESC",
            [$clienteId]
        );
    }

}

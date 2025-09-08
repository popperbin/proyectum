<?php
require_once __DIR__ . "/../config/db.php";

class Lista {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Listar todas las listas con sus tareas para un proyecto
    public function listarPorProyecto($proyecto_id) {
        $sql = "SELECT * FROM listas WHERE proyecto_id = ? ORDER BY id ASC";
        $listas = $this->db->fetchAll($sql, [$proyecto_id]);

        // Cargar tareas asociadas
        foreach ($listas as &$lista) {
            $sqlT = "SELECT * FROM tareas WHERE lista_id = ? ORDER BY id ASC";
            $lista['tareas'] = $this->db->fetchAll($sqlT, [$lista['id']]);
        }

        return $listas;
    }

    // Crear lista nueva
    public function crear($nombre, $proyecto_id) {
        $sql = "INSERT INTO listas (nombre, proyecto_id) VALUES (?, ?)";
        return $this->db->insert($sql, [$nombre, $proyecto_id]);
    }

    // archivar  lista y sus tareas
    public function archivar($id, $proyecto_id) {
        $sql = "UPDATE listas SET estado = 'archivado' WHERE id = ? AND proyecto_id = ?";
        return $this->db->execute($sql, [$id, $proyecto_id]);
    }


}

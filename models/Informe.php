<?php
require_once __DIR__ . "/../config/db.php";

class Informe {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function listarPorProyecto($proyecto_id) {
        $sql = "SELECT i.*, u.nombres as autor_nombre, u.apellidos as autor_apellido
                FROM informes i
                INNER JOIN usuarios u ON i.generado_por = u.id
                WHERE i.proyecto_id = ?
                ORDER BY i.fecha_generacion DESC";

        return $this->db->fetchAll($sql, [$proyecto_id]);
    }

    public function obtenerPorId($id) {
        return $this->db->fetchOne("SELECT * FROM informes WHERE id = ?", [$id]);
    }

    public function crear($data) {
        $sql = "INSERT INTO informes 
                (proyecto_id, titulo, contenido, tipo, archivo_pdf, generado_por, comentarios, observaciones)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->insert($sql, [
            $data['proyecto_id'],
            $data['titulo'],
            $data['contenido'] ?? null,
            $data['tipo'] ?? 'progreso',
            $data['archivo_pdf'] ?? null,
            $data['generado_por'],
            $data['comentarios'] ?? null,
            $data['observaciones'] ?? null
        ]);
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE informes SET titulo=?, contenido=?, tipo=?, archivo_pdf=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['titulo'],
            $data['contenido'] ?? null,
            $data['tipo'],
            $data['archivo_pdf'] ?? null,
            $id
        ]);
    }

    // ✅ Nuevo: actualizar solo la URL del PDF
    public function actualizarPdf($id, $pdfUrl) {
        $sql = "UPDATE informes SET archivo_pdf=? WHERE id=?";
        return $this->db->execute($sql, [$pdfUrl, $id]);
    }

    public function eliminar($id) {
        return $this->db->execute("DELETE FROM informes WHERE id = ?", [$id]);
    }
}
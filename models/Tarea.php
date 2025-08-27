<?php
require_once 'config/db.php';

class Tarea {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO tareas (titulo, descripcion, estado, prioridad, fecha_vencimiento, proyecto_id, asignado_a, creado_por, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $datos['titulo'],
                $datos['descripcion'],
                $datos['estado'] ?? 'pendiente',
                $datos['prioridad'] ?? 'media',
                $datos['fecha_vencimiento'],
                $datos['proyecto_id'],
                $datos['asignado_a'],
                $datos['creado_por']
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al crear tarea: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerPorProyecto($proyecto_id) {
        try {
            $sql = "SELECT t.*, 
                           u_asignado.nombre as asignado_nombre,
                           u_creador.nombre as creador_nombre,
                           p.nombre as proyecto_nombre
                    FROM tareas t
                    LEFT JOIN usuarios u_asignado ON t.asignado_a = u_asignado.id
                    LEFT JOIN usuarios u_creador ON t.creado_por = u_creador.id
                    LEFT JOIN proyectos p ON t.proyecto_id = p.id
                    WHERE t.proyecto_id = ?
                    ORDER BY 
                        CASE t.prioridad 
                            WHEN 'alta' THEN 1 
                            WHEN 'media' THEN 2 
                            WHEN 'baja' THEN 3 
                        END,
                        t.fecha_vencimiento ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$proyecto_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener tareas por proyecto: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerPorUsuario($usuario_id) {
        try {
            $sql = "SELECT t.*, 
                           u_asignado.nombre as asignado_nombre,
                           u_creador.nombre as creador_nombre,
                           p.nombre as proyecto_nombre
                    FROM tareas t
                    LEFT JOIN usuarios u_asignado ON t.asignado_a = u_asignado.id
                    LEFT JOIN usuarios u_creador ON t.creado_por = u_creador.id
                    LEFT JOIN proyectos p ON t.proyecto_id = p.id
                    WHERE t.asignado_a = ?
                    ORDER BY 
                        CASE t.prioridad 
                            WHEN 'alta' THEN 1 
                            WHEN 'media' THEN 2 
                            WHEN 'baja' THEN 3 
                        END,
                        t.fecha_vencimiento ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usuario_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener tareas por usuario: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerTablero($proyecto_id = null, $usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT t.*, 
                           u_asignado.nombre as asignado_nombre,
                           u_creador.nombre as creador_nombre,
                           p.nombre as proyecto_nombre
                    FROM tareas t
                    LEFT JOIN usuarios u_asignado ON t.asignado_a = u_asignado.id
                    LEFT JOIN usuarios u_creador ON t.creado_por = u_creador.id
                    LEFT JOIN proyectos p ON t.proyecto_id = p.id";
            
            $conditions = [];
            $params = [];
            
            if ($proyecto_id) {
                $conditions[] = "t.proyecto_id = ?";
                $params[] = $proyecto_id;
            }
            
            // Filtrar según el rol
            if ($rol === 'colaborador') {
                $conditions[] = "t.asignado_a = ?";
                $params[] = $usuario_id;
            } elseif ($rol === 'gestor') {
                $conditions[] = "p.gestor_id = ?";
                $params[] = $usuario_id;
            } elseif ($rol === 'cliente') {
                $conditions[] = "p.cliente_id = ?";
                $params[] = $usuario_id;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $sql .= " ORDER BY 
                        CASE t.prioridad 
                            WHEN 'alta' THEN 1 
                            WHEN 'media' THEN 2 
                            WHEN 'baja' THEN 3 
                        END,
                        t.fecha_vencimiento ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Organizar por estado para el tablero Kanban
            $tablero = [
                'pendiente' => [],
                'en_progreso' => [],
                'en_revision' => [],
                'completada' => []
            ];
            
            foreach ($tareas as $tarea) {
                $estado = $tarea['estado'];
                if (array_key_exists($estado, $tablero)) {
                    $tablero[$estado][] = $tarea;
                }
            }
            
            return $tablero;
            
        } catch (PDOException $e) {
            error_log("Error al obtener tablero: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT t.*, 
                           u_asignado.nombre as asignado_nombre,
                           u_asignado.email as asignado_email,
                           u_creador.nombre as creador_nombre,
                           p.nombre as proyecto_nombre
                    FROM tareas t
                    LEFT JOIN usuarios u_asignado ON t.asignado_a = u_asignado.id
                    LEFT JOIN usuarios u_creador ON t.creado_por = u_creador.id
                    LEFT JOIN proyectos p ON t.proyecto_id = p.id
                    WHERE t.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener tarea: " . $e->getMessage());
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE tareas SET 
                    titulo = ?, 
                    descripcion = ?, 
                    estado = ?, 
                    prioridad = ?, 
                    fecha_vencimiento = ?, 
                    asignado_a = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $datos['titulo'],
                $datos['descripcion'],
                $datos['estado'],
                $datos['prioridad'],
                $datos['fecha_vencimiento'],
                $datos['asignado_a'],
                $id
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar tarea: " . $e->getMessage());
            return false;
        }
    }
    
    public function cambiarEstado($id, $nuevo_estado, $usuario_id = null) {
        try {
            $sql = "UPDATE tareas SET estado = ?, updated_at = NOW()";
            $params = [$nuevo_estado];
            
            // Si se completa la tarea, registrar quien la completó y cuándo
            if ($nuevo_estado === 'completada') {
                $sql .= ", completada_por = ?, completada_en = NOW()";
                $params[] = $usuario_id;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de tarea: " . $e->getMessage());
            return false;
        }
    }
    
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM tareas WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$id]);
            
        } catch (PDOException $e) {
            error_log("Error al eliminar tarea: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerEstadisticas($usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_tareas,
                        COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as tareas_pendientes,
                        COUNT(CASE WHEN estado = 'en_progreso' THEN 1 END) as tareas_en_progreso,
                        COUNT(CASE WHEN estado = 'completada' THEN 1 END) as tareas_completadas,
                        COUNT(CASE WHEN fecha_vencimiento < CURDATE() AND estado != 'completada' THEN 1 END) as tareas_vencidas
                    FROM tareas t
                    LEFT JOIN proyectos p ON t.proyecto_id = p.id";
            
            $params = [];
            
            // Filtrar según el rol
            if ($rol === 'colaborador') {
                $sql .= " WHERE t.asignado_a = ?";
                $params[] = $usuario_id;
            } elseif ($rol === 'gestor') {
                $sql .= " WHERE p.gestor_id = ?";
                $params[] = $usuario_id;
            } elseif ($rol === 'cliente') {
                $sql .= " WHERE p.cliente_id = ?";
                $params[] = $usuario_id;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de tareas: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerTareasVencidas($usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT t.*, 
                           u_asignado.nombre as asignado_nombre,
                           p.nombre as proyecto_nombre
                    FROM tareas t
                    LEFT JOIN usuarios u_asignado ON t.asignado_a = u_asignado.id
                    LEFT JOIN proyectos p ON t.proyecto_id = p.id
                    WHERE t.fecha_vencimiento < CURDATE() AND t.estado != 'completada'";
            
            $params = [];
            
            // Filtrar según el rol
            if ($rol === 'colaborador') {
                $sql .= " AND t.asignado_a = ?";
                $params[] = $usuario_id;
            } elseif ($rol === 'gestor') {
                $sql .= " AND p.gestor_id = ?";
                $params[] = $usuario_id;
            } elseif ($rol === 'cliente') {
                $sql .= " AND p.cliente_id = ?";
                $params[] = $usuario_id;
            }
            
            $sql .= " ORDER BY t.fecha_vencimiento ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener tareas vencidas: " . $e->getMessage());
            return [];
        }
    }
    
    public function buscar($termino, $usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT t.*, 
                           u_asignado.nombre as asignado_nombre,
                           u_creador.nombre as creador_nombre,
                           p.nombre as proyecto_nombre
                    FROM tareas t
                    LEFT JOIN usuarios u_asignado ON t.asignado_a = u_asignado.id
                    LEFT JOIN usuarios u_creador ON t.creado_por = u_creador.id
                    LEFT JOIN proyectos p ON t.proyecto_id = p.id
                    WHERE (t.titulo LIKE ? OR t.descripcion LIKE ?)";
            
            $params = ["%$termino%", "%$termino%"];
            
            // Aplicar filtros según el rol
            if ($rol === 'colaborador') {
                $sql .= " AND t.asignado_a = ?";
                $params[] = $usuario_id;
            } elseif ($rol === 'gestor') {
                $sql .= " AND p.gestor_id = ?";
                $params[] = $usuario_id;
            } elseif ($rol === 'cliente') {
                $sql .= " AND p.cliente_id = ?";
                $params[] = $usuario_id;
            }
            
            $sql .= " ORDER BY t.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al buscar tareas: " . $e->getMessage());
            return [];
        }
    }
}
?>
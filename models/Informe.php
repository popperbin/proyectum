<?php
require_once 'config/db.php';

class Informe {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO informes (titulo, tipo, descripcion, fecha_inicio, fecha_fin, proyecto_id, generado_por, ruta_archivo, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $datos['titulo'],
                $datos['tipo'],
                $datos['descripcion'],
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $datos['proyecto_id'] ?? null,
                $datos['generado_por'],
                $datos['ruta_archivo'] ?? null
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al crear informe: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerTodos($usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT i.*, 
                           u.nombre as generado_nombre,
                           p.nombre as proyecto_nombre
                    FROM informes i
                    LEFT JOIN usuarios u ON i.generado_por = u.id
                    LEFT JOIN proyectos p ON i.proyecto_id = p.id";
            
            $params = [];
            
            // Filtrar según el rol
            if ($rol !== 'admin') {
                if ($rol === 'cliente') {
                    $sql .= " WHERE (p.cliente_id = ? OR i.proyecto_id IS NULL)";
                    $params[] = $usuario_id;
                } elseif ($rol === 'gestor') {
                    $sql .= " WHERE (p.gestor_id = ? OR i.generado_por = ?)";
                    $params[] = $usuario_id;
                    $params[] = $usuario_id;
                } elseif ($rol === 'colaborador') {
                    $sql .= " WHERE (p.id IN (
                        SELECT DISTINCT proyecto_id FROM tareas WHERE asignado_a = ?
                    ) OR i.generado_por = ?)";
                    $params[] = $usuario_id;
                    $params[] = $usuario_id;
                }
            }
            
            $sql .= " ORDER BY i.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener informes: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT i.*, 
                           u.nombre as generado_nombre,
                           u.email as generado_email,
                           p.nombre as proyecto_nombre
                    FROM informes i
                    LEFT JOIN usuarios u ON i.generado_por = u.id
                    LEFT JOIN proyectos p ON i.proyecto_id = p.id
                    WHERE i.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener informe: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerPorProyecto($proyecto_id) {
        try {
            $sql = "SELECT i.*, 
                           u.nombre as generado_nombre
                    FROM informes i
                    LEFT JOIN usuarios u ON i.generado_por = u.id
                    WHERE i.proyecto_id = ?
                    ORDER BY i.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$proyecto_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener informes por proyecto: " . $e->getMessage());
            return [];
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE informes SET 
                    titulo = ?, 
                    descripcion = ?, 
                    ruta_archivo = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $datos['titulo'],
                $datos['descripcion'],
                $datos['ruta_archivo'],
                $id
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar informe: " . $e->getMessage());
            return false;
        }
    }
    
    public function eliminar($id) {
        try {
            // Obtener la ruta del archivo antes de eliminar
            $informe = $this->obtenerPorId($id);
            
            $sql = "DELETE FROM informes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if ($stmt->execute([$id])) {
                // Eliminar archivo físico si existe
                if ($informe && $informe['ruta_archivo'] && file_exists($informe['ruta_archivo'])) {
                    unlink($informe['ruta_archivo']);
                }
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al eliminar informe: " . $e->getMessage());
            return false;
        }
    }
    
    public function generarInformeProyecto($proyecto_id, $usuario_id) {
        try {
            // Obtener datos del proyecto
            $sql_proyecto = "SELECT p.*, 
                                   u_cliente.nombre as cliente_nombre,
                                   u_gestor.nombre as gestor_nombre
                            FROM proyectos p
                            LEFT JOIN usuarios u_cliente ON p.cliente_id = u_cliente.id
                            LEFT JOIN usuarios u_gestor ON p.gestor_id = u_gestor.id
                            WHERE p.id = ?";
            
            $stmt = $this->db->prepare($sql_proyecto);
            $stmt->execute([$proyecto_id]);
            $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$proyecto) {
                return false;
            }
            
            // Obtener tareas del proyecto
            $sql_tareas = "SELECT t.*, u.nombre as asignado_nombre
                          FROM tareas t
                          LEFT JOIN usuarios u ON t.asignado_a = u.id
                          WHERE t.proyecto_id = ?";
            
            $stmt = $this->db->prepare($sql_tareas);
            $stmt->execute([$proyecto_id]);
            $tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener riesgos del proyecto
            $sql_riesgos = "SELECT r.*, u.nombre as identificado_nombre
                           FROM riesgos r
                           LEFT JOIN usuarios u ON r.identificado_por = u.id
                           WHERE r.proyecto_id = ?";
            
            $stmt = $this->db->prepare($sql_riesgos);
            $stmt->execute([$proyecto_id]);
            $riesgos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular estadísticas
            $total_tareas = count($tareas);
            $tareas_completadas = count(array_filter($tareas, function($t) { return $t['estado'] === 'completada'; }));
            $progreso = $total_tareas > 0 ? round(($tareas_completadas / $total_tareas) * 100, 2) : 0;
            
            $riesgos_altos = count(array_filter($riesgos, function($r) { return $r['nivel_riesgo'] === 'alto'; }));
            
            $datos_informe = [
                'proyecto' => $proyecto,
                'tareas' => $tareas,
                'riesgos' => $riesgos,
                'estadisticas' => [
                    'total_tareas' => $total_tareas,
                    'tareas_completadas' => $tareas_completadas,
                    'progreso' => $progreso,
                    'riesgos_altos' => $riesgos_altos
                ]
            ];
            
            return $datos_informe;
            
        } catch (PDOException $e) {
            error_log("Error al generar informe del proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    public function generarInformeGeneral($usuario_id, $rol, $fecha_inicio = null, $fecha_fin = null) {
        try {
            $datos = [];
            
            // Estadísticas de proyectos
            $sql_proyectos = "SELECT 
                                COUNT(*) as total_proyectos,
                                COUNT(CASE WHEN estado = 'activo' THEN 1 END) as proyectos_activos,
                                COUNT(CASE WHEN estado = 'completado' THEN 1 END) as proyectos_completados,
                                AVG(presupuesto) as presupuesto_promedio
                             FROM proyectos p";
            
            $params = [];
            
            // Aplicar filtros de fecha si se proporcionan
            $where_conditions = [];
            if ($fecha_inicio && $fecha_fin) {
                $where_conditions[] = "p.created_at BETWEEN ? AND ?";
                $params[] = $fecha_inicio;
                $params[] = $fecha_fin;
            }
            
            // Filtrar según el rol
            if ($rol !== 'admin') {
                if ($rol === 'cliente') {
                    $where_conditions[] = "p.cliente_id = ?";
                    $params[] = $usuario_id;
                } elseif ($rol === 'gestor') {
                    $where_conditions[] = "p.gestor_id = ?";
                    $params[] = $usuario_id;
                }
            }
            
            if (!empty($where_conditions)) {
                $sql_proyectos .= " WHERE " . implode(" AND ", $where_conditions);
            }
            
            $stmt = $this->db->prepare($sql_proyectos);
            $stmt->execute($params);
            $datos['proyectos'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Estadísticas de tareas
            $sql_tareas = "SELECT 
                             COUNT(*) as total_tareas,
                             COUNT(CASE WHEN t.estado = 'completada' THEN 1 END) as tareas_completadas,
                             COUNT(CASE WHEN t.fecha_vencimiento < CURDATE() AND t.estado != 'completada' THEN 1 END) as tareas_vencidas
                          FROM tareas t
                          LEFT JOIN proyectos p ON t.proyecto_id = p.id";
            
            $params_tareas = [];
            $where_conditions_tareas = [];
            
            if ($fecha_inicio && $fecha_fin) {
                $where_conditions_tareas[] = "t.created_at BETWEEN ? AND ?";
                $params_tareas[] = $fecha_inicio;
                $params_tareas[] = $fecha_fin;
            }
            
            if ($rol !== 'admin') {
                if ($rol === 'cliente') {
                    $where_conditions_tareas[] = "p.cliente_id = ?";
                    $params_tareas[] = $usuario_id;
                } elseif ($rol === 'gestor') {
                    $where_conditions_tareas[] = "p.gestor_id = ?";
                    $params_tareas[] = $usuario_id;
                } elseif ($rol === 'colaborador') {
                    $where_conditions_tareas[] = "t.asignado_a = ?";
                    $params_tareas[] = $usuario_id;
                }
            }
            
            if (!empty($where_conditions_tareas)) {
                $sql_tareas .= " WHERE " . implode(" AND ", $where_conditions_tareas);
            }
            
            $stmt = $this->db->prepare($sql_tareas);
            $stmt->execute($params_tareas);
            $datos['tareas'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Estadísticas de riesgos
            $sql_riesgos = "SELECT 
                              COUNT(*) as total_riesgos,
                              COUNT(CASE WHEN r.nivel_riesgo = 'alto' THEN 1 END) as riesgos_altos,
                              COUNT(CASE WHEN r.estado = 'activo' THEN 1 END) as riesgos_activos
                           FROM riesgos r
                           LEFT JOIN proyectos p ON r.proyecto_id = p.id";
            
            $params_riesgos = [];
            $where_conditions_riesgos = [];
            
            if ($fecha_inicio && $fecha_fin) {
                $where_conditions_riesgos[] = "r.created_at BETWEEN ? AND ?";
                $params_riesgos[] = $fecha_inicio;
                $params_riesgos[] = $fecha_fin;
            }
            
            if ($rol !== 'admin') {
                if ($rol === 'cliente') {
                    $where_conditions_riesgos[] = "p.cliente_id = ?";
                    $params_riesgos[] = $usuario_id;
                } elseif ($rol === 'gestor') {
                    $where_conditions_riesgos[] = "p.gestor_id = ?";
                    $params_riesgos[] = $usuario_id;
                } elseif ($rol === 'colaborador') {
                    $where_conditions_riesgos[] = "p.id IN (SELECT DISTINCT proyecto_id FROM tareas WHERE asignado_a = ?)";
                    $params_riesgos[] = $usuario_id;
                }
            }
            
            if (!empty($where_conditions_riesgos)) {
                $sql_riesgos .= " WHERE " . implode(" AND ", $where_conditions_riesgos);
            }
            
            $stmt = $this->db->prepare($sql_riesgos);
            $stmt->execute($params_riesgos);
            $datos['riesgos'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $datos;
            
        } catch (PDOException $e) {
            error_log("Error al generar informe general: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerInformesRecientes($usuario_id = null, $rol = null, $limit = 5) {
        try {
            $sql = "SELECT i.*, 
                           u.nombre as generado_nombre,
                           p.nombre as proyecto_nombre
                    FROM informes i
                    LEFT JOIN usuarios u ON i.generado_por = u.id
                    LEFT JOIN proyectos p ON i.proyecto_id = p.id";
            
            $params = [];
            
            // Filtrar según el rol
            if ($rol !== 'admin') {
                if ($rol === 'cliente') {
                    $sql .= " WHERE (p.cliente_id = ? OR i.proyecto_id IS NULL)";
                    $params[] = $usuario_id;
                } elseif ($rol === 'gestor') {
                    $sql .= " WHERE (p.gestor_id = ? OR i.generado_por = ?)";
                    $params[] = $usuario_id;
                    $params[] = $usuario_id;
                } elseif ($rol === 'colaborador') {
                    $sql .= " WHERE i.generado_por = ?";
                    $params[] = $usuario_id;
                }
            }
            
            $sql .= " ORDER BY i.created_at DESC LIMIT ?";
            $params[] = $limit;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener informes recientes: " . $e->getMessage());
            return [];
        }
    }
}
?>
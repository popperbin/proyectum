<?php
require_once 'config/db.php';

class Proyecto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO proyectos (nombre, descripcion, fecha_inicio, fecha_fin, estado, presupuesto, cliente_id, gestor_id, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'],
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $datos['estado'] ?? 'planificacion',
                $datos['presupuesto'] ?? 0,
                $datos['cliente_id'],
                $datos['gestor_id']
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al crear proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerTodos($usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT p.*, 
                           u_cliente.nombre as cliente_nombre,
                           u_gestor.nombre as gestor_nombre,
                           COUNT(t.id) as total_tareas,
                           COUNT(CASE WHEN t.estado = 'completada' THEN 1 END) as tareas_completadas
                    FROM proyectos p
                    LEFT JOIN usuarios u_cliente ON p.cliente_id = u_cliente.id
                    LEFT JOIN usuarios u_gestor ON p.gestor_id = u_gestor.id
                    LEFT JOIN tareas t ON p.id = t.proyecto_id";
            
            $params = [];
            
            // Filtrar según el rol del usuario
            if ($rol !== 'admin') {
                if ($rol === 'cliente') {
                    $sql .= " WHERE p.cliente_id = ?";
                    $params[] = $usuario_id;
                } elseif ($rol === 'gestor') {
                    $sql .= " WHERE p.gestor_id = ?";
                    $params[] = $usuario_id;
                } elseif ($rol === 'colaborador') {
                    $sql .= " WHERE p.id IN (
                        SELECT DISTINCT proyecto_id FROM tareas WHERE asignado_a = ?
                    )";
                    $params[] = $usuario_id;
                }
            }
            
            $sql .= " GROUP BY p.id ORDER BY p.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener proyectos: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT p.*, 
                           u_cliente.nombre as cliente_nombre,
                           u_cliente.email as cliente_email,
                           u_gestor.nombre as gestor_nombre,
                           u_gestor.email as gestor_email
                    FROM proyectos p
                    LEFT JOIN usuarios u_cliente ON p.cliente_id = u_cliente.id
                    LEFT JOIN usuarios u_gestor ON p.gestor_id = u_gestor.id
                    WHERE p.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE proyectos SET 
                    nombre = ?, 
                    descripcion = ?, 
                    fecha_inicio = ?, 
                    fecha_fin = ?, 
                    estado = ?, 
                    presupuesto = ?,
                    cliente_id = ?,
                    gestor_id = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $datos['nombre'],
                $datos['descripcion'],
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $datos['estado'],
                $datos['presupuesto'],
                $datos['cliente_id'],
                $datos['gestor_id'],
                $id
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    public function eliminar($id) {
        try {
            // Verificar si hay tareas asociadas
            $sql_check = "SELECT COUNT(*) as total FROM tareas WHERE proyecto_id = ?";
            $stmt_check = $this->db->prepare($sql_check);
            $stmt_check->execute([$id]);
            $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar un proyecto con tareas asociadas'];
            }
            
            $sql = "DELETE FROM proyectos WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if ($stmt->execute([$id])) {
                return ['success' => true, 'message' => 'Proyecto eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al eliminar el proyecto'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al eliminar proyecto: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }
    
    public function cambiarEstado($id, $nuevo_estado) {
        try {
            $sql = "UPDATE proyectos SET estado = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$nuevo_estado, $id]);
            
        } catch (PDOException $e) {
            error_log("Error al cambiar estado del proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerEstadisticas($usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_proyectos,
                        COUNT(CASE WHEN estado = 'activo' THEN 1 END) as proyectos_activos,
                        COUNT(CASE WHEN estado = 'completado' THEN 1 END) as proyectos_completados,
                        COUNT(CASE WHEN estado = 'pausado' THEN 1 END) as proyectos_pausados,
                        AVG(presupuesto) as presupuesto_promedio
                    FROM proyectos";
            
            $params = [];
            
            // Filtrar según el rol
            if ($rol !== 'admin') {
                if ($rol === 'cliente') {
                    $sql .= " WHERE cliente_id = ?";
                    $params[] = $usuario_id;
                } elseif ($rol === 'gestor') {
                    $sql .= " WHERE gestor_id = ?";
                    $params[] = $usuario_id;
                } elseif ($rol === 'colaborador') {
                    $sql .= " WHERE id IN (
                        SELECT DISTINCT proyecto_id FROM tareas WHERE asignado_a = ?
                    )";
                    $params[] = $usuario_id;
                }
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }
    
    public function buscar($termino, $usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT p.*, 
                           u_cliente.nombre as cliente_nombre,
                           u_gestor.nombre as gestor_nombre
                    FROM proyectos p
                    LEFT JOIN usuarios u_cliente ON p.cliente_id = u_cliente.id
                    LEFT JOIN usuarios u_gestor ON p.gestor_id = u_gestor.id
                    WHERE (p.nombre LIKE ? OR p.descripcion LIKE ?)";
            
            $params = ["%$termino%", "%$termino%"];
            
            // Aplicar filtros según el rol
            if ($rol !== 'admin') {
                if ($rol === 'cliente') {
                    $sql .= " AND p.cliente_id = ?";
                    $params[] = $usuario_id;
                } elseif ($rol === 'gestor') {
                    $sql .= " AND p.gestor_id = ?";
                    $params[] = $usuario_id;
                } elseif ($rol === 'colaborador') {
                    $sql .= " AND p.id IN (
                        SELECT DISTINCT proyecto_id FROM tareas WHERE asignado_a = ?
                    )";
                    $params[] = $usuario_id;
                }
            }
            
            $sql .= " ORDER BY p.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al buscar proyectos: " . $e->getMessage());
            return [];
        }
    }
}
?>
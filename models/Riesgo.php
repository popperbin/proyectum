<?php
require_once 'config/db.php';

class Riesgo {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO riesgos (titulo, descripcion, categoria, probabilidad, impacto, nivel_riesgo, estado, plan_mitigacion, proyecto_id, identificado_por, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $nivel_riesgo = $this->calcularNivelRiesgo($datos['probabilidad'], $datos['impacto']);
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $datos['titulo'],
                $datos['descripcion'],
                $datos['categoria'],
                $datos['probabilidad'],
                $datos['impacto'],
                $nivel_riesgo,
                $datos['estado'] ?? 'identificado',
                $datos['plan_mitigacion'] ?? '',
                $datos['proyecto_id'],
                $datos['identificado_por']
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al crear riesgo: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerPorProyecto($proyecto_id) {
        try {
            $sql = "SELECT r.*, 
                           u.nombre as identificado_nombre,
                           p.nombre as proyecto_nombre
                    FROM riesgos r
                    LEFT JOIN usuarios u ON r.identificado_por = u.id
                    LEFT JOIN proyectos p ON r.proyecto_id = p.id
                    WHERE r.proyecto_id = ?
                    ORDER BY 
                        CASE r.nivel_riesgo 
                            WHEN 'alto' THEN 1 
                            WHEN 'medio' THEN 2 
                            WHEN 'bajo' THEN 3 
                        END,
                        r.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$proyecto_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener riesgos por proyecto: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerTodos($usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT r.*, 
                           u.nombre as identificado_nombre,
                           p.nombre as proyecto_nombre
                    FROM riesgos r
                    LEFT JOIN usuarios u ON r.identificado_por = u.id
                    LEFT JOIN proyectos p ON r.proyecto_id = p.id";
            
            $params = [];
            
            // Filtrar según el rol
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
            
            $sql .= " ORDER BY 
                        CASE r.nivel_riesgo 
                            WHEN 'alto' THEN 1 
                            WHEN 'medio' THEN 2 
                            WHEN 'bajo' THEN 3 
                        END,
                        r.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener riesgos: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT r.*, 
                           u.nombre as identificado_nombre,
                           u.email as identificado_email,
                           p.nombre as proyecto_nombre
                    FROM riesgos r
                    LEFT JOIN usuarios u ON r.identificado_por = u.id
                    LEFT JOIN proyectos p ON r.proyecto_id = p.id
                    WHERE r.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener riesgo: " . $e->getMessage());
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $nivel_riesgo = $this->calcularNivelRiesgo($datos['probabilidad'], $datos['impacto']);
            
            $sql = "UPDATE riesgos SET 
                    titulo = ?, 
                    descripcion = ?, 
                    categoria = ?, 
                    probabilidad = ?, 
                    impacto = ?, 
                    nivel_riesgo = ?,
                    estado = ?, 
                    plan_mitigacion = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $datos['titulo'],
                $datos['descripcion'],
                $datos['categoria'],
                $datos['probabilidad'],
                $datos['impacto'],
                $nivel_riesgo,
                $datos['estado'],
                $datos['plan_mitigacion'],
                $id
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar riesgo: " . $e->getMessage());
            return false;
        }
    }
    
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM riesgos WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$id]);
            
        } catch (PDOException $e) {
            error_log("Error al eliminar riesgo: " . $e->getMessage());
            return false;
        }
    }
    
    public function cambiarEstado($id, $nuevo_estado) {
        try {
            $sql = "UPDATE riesgos SET estado = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$nuevo_estado, $id]);
            
        } catch (PDOException $e) {
            error_log("Error al cambiar estado del riesgo: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerEstadisticas($usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_riesgos,
                        COUNT(CASE WHEN nivel_riesgo = 'alto' THEN 1 END) as riesgos_altos,
                        COUNT(CASE WHEN nivel_riesgo = 'medio' THEN 1 END) as riesgos_medios,
                        COUNT(CASE WHEN nivel_riesgo = 'bajo' THEN 1 END) as riesgos_bajos,
                        COUNT(CASE WHEN estado = 'activo' THEN 1 END) as riesgos_activos,
                        COUNT(CASE WHEN estado = 'mitigado' THEN 1 END) as riesgos_mitigados
                    FROM riesgos r
                    LEFT JOIN proyectos p ON r.proyecto_id = p.id";
            
            $params = [];
            
            // Filtrar según el rol
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
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de riesgos: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerRiesgosAltos($usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT r.*, 
                           u.nombre as identificado_nombre,
                           p.nombre as proyecto_nombre
                    FROM riesgos r
                    LEFT JOIN usuarios u ON r.identificado_por = u.id
                    LEFT JOIN proyectos p ON r.proyecto_id = p.id
                    WHERE r.nivel_riesgo = 'alto' AND r.estado = 'activo'";
            
            $params = [];
            
            // Filtrar según el rol
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
            
            $sql .= " ORDER BY r.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener riesgos altos: " . $e->getMessage());
            return [];
        }
    }
    
    private function calcularNivelRiesgo($probabilidad, $impacto) {
        // Matriz de riesgo: probabilidad x impacto
        $matriz = [
            'baja' => ['bajo' => 'bajo', 'medio' => 'bajo', 'alto' => 'medio'],
            'media' => ['bajo' => 'bajo', 'medio' => 'medio', 'alto' => 'alto'],
            'alta' => ['bajo' => 'medio', 'medio' => 'alto', 'alto' => 'alto']
        ];
        
        return $matriz[$probabilidad][$impacto] ?? 'medio';
    }
    
    public function obtenerPorCategoria($categoria, $usuario_id = null, $rol = null) {
        try {
            $sql = "SELECT r.*, 
                           u.nombre as identificado_nombre,
                           p.nombre as proyecto_nombre
                    FROM riesgos r
                    LEFT JOIN usuarios u ON r.identificado_por = u.id
                    LEFT JOIN proyectos p ON r.proyecto_id = p.id
                    WHERE r.categoria = ?";
            
            $params = [$categoria];
            
            // Filtrar según el rol
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
            
            $sql .= " ORDER BY r.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener riesgos por categoría: " . $e->getMessage());
            return [];
        }
    }
}
?>
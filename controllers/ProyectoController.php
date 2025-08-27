<?php
session_start();
require_once '../config/db.php';
require_once '../models/Proyecto.php';

class ProyectoController {
    private $proyecto;
    
    public function __construct() {
        $this->proyecto = new Proyecto();
    }
    
    public function crear($datos) {
        // Validar datos obligatorios
        if (empty($datos['nombre']) || empty($datos['fecha_inicio']) || empty($datos['fecha_fin'])) {
            return ['success' => false, 'message' => 'Nombre, fecha de inicio y fecha de fin son obligatorios'];
        }
        
        // Validar fechas
        if (strtotime($datos['fecha_inicio']) > strtotime($datos['fecha_fin'])) {
            return ['success' => false, 'message' => 'La fecha de inicio no puede ser posterior a la fecha de fin'];
        }
        
        $resultado = $this->proyecto->crear($datos);
        
        if ($resultado) {
            return ['success' => true, 'message' => 'Proyecto creado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al crear el proyecto'];
        }
    }
    
    public function listar($usuario_id = null, $rol = null) {
        return $this->proyecto->obtenerTodos($usuario_id, $rol);
    }
    
    public function obtener($id) {
        return $this->proyecto->obtenerPorId($id);
    }
    
    public function actualizar($id, $datos) {
        // Validar datos
        if (empty($datos['nombre']) || empty($datos['fecha_inicio']) || empty($datos['fecha_fin'])) {
            return ['success' => false, 'message' => 'Nombre, fecha de inicio y fecha de fin son obligatorios'];
        }
        
        if (strtotime($datos['fecha_inicio']) > strtotime($datos['fecha_fin'])) {
            return ['success' => false, 'message' => 'La fecha de inicio no puede ser posterior a la fecha de fin'];
        }
        
        $resultado = $this->proyecto->actualizar($id, $datos);
        
        if ($resultado) {
            return ['success' => true, 'message' => 'Proyecto actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar el proyecto'];
        }
    }
    
    public function eliminar($id) {
        return $this->proyecto->eliminar($id);
    }
    
    public function cambiarEstado($id, $nuevo_estado) {
        $estados_validos = ['planificacion', 'activo', 'pausado', 'completado', 'cancelado'];
        
        if (!in_array($nuevo_estado, $estados_validos)) {
            return ['success' => false, 'message' => 'Estado no válido'];
        }
        
        $resultado = $this->proyecto->cambiarEstado($id, $nuevo_estado);
        
        if ($resultado) {
            return ['success' => true, 'message' => 'Estado actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar el estado'];
        }
    }
    
    public function obtenerEstadisticas($usuario_id = null, $rol = null) {
        return $this->proyecto->obtenerEstadisticas($usuario_id, $rol);
    }
    
    public function buscar($termino, $usuario_id = null, $rol = null) {
        return $this->proyecto->buscar($termino, $usuario_id, $rol);
    }
}

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Manejar peticiones POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new ProyectoController();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Solo admin y gestor pueden crear proyectos
                if ($rol !== 'admin' && $rol !== 'gestor') {
                    header('Location: ../dashboard.php?error=permission');
                    exit();
                }
                
                $datos = [
                    'nombre' => $_POST['nombre'] ?? '',
                    'descripcion' => $_POST['descripcion'] ?? '',
                    'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
                    'fecha_fin' => $_POST['fecha_fin'] ?? '',
                    'estado' => $_POST['estado'] ?? 'planificacion',
                    'presupuesto' => $_POST['presupuesto'] ?? 0,
                    'cliente_id' => $_POST['cliente_id'] ?? null,
                    'gestor_id' => $rol === 'admin' ? ($_POST['gestor_id'] ?? $usuario_id) : $usuario_id
                ];
                
                $resultado = $controller->crear($datos);
                
                if ($resultado['success']) {
                    header('Location: ../views/proyectos/listar.php?success=created');
                } else {
                    header('Location: ../views/proyectos/crear.php?error=' . urlencode($resultado['message']));
                }
                exit();
                break;
                
            case 'update':
                // Verificar permisos
                $proyecto = $controller->obtener($_POST['id']);
                if (!$proyecto || ($rol === 'gestor' && $proyecto['gestor_id'] != $usuario_id) || 
                    ($rol === 'cliente' && $proyecto['cliente_id'] != $usuario_id)) {
                    header('Location: ../views/proyectos/listar.php?error=permission');
                    exit();
                }
                
                $datos = [
                    'nombre' => $_POST['nombre'] ?? '',
                    'descripcion' => $_POST['descripcion'] ?? '',
                    'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
                    'fecha_fin' => $_POST['fecha_fin'] ?? '',
                    'estado' => $_POST['estado'] ?? 'planificacion',
                    'presupuesto' => $_POST['presupuesto'] ?? 0,
                    'cliente_id' => $_POST['cliente_id'] ?? null,
                    'gestor_id' => $_POST['gestor_id'] ?? $proyecto['gestor_id']
                ];
                
                $resultado = $controller->actualizar($_POST['id'], $datos);
                
                if ($resultado['success']) {
                    header('Location: ../views/proyectos/listar.php?success=updated');
                } else {
                    header('Location: ../views/proyectos/editar.php?id=' . $_POST['id'] . '&error=' . urlencode($resultado['message']));
                }
                exit();
                break;
                
            case 'change_status':
                $id = $_POST['id'] ?? 0;
                $nuevo_estado = $_POST['estado'] ?? '';
                
                // Verificar permisos
                $proyecto = $controller->obtener($id);
                if (!$proyecto || ($rol === 'gestor' && $proyecto['gestor_id'] != $usuario_id)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
                    exit();
                }
                
                $resultado = $controller->cambiarEstado($id, $nuevo_estado);
                
                header('Content-Type: application/json');
                echo json_encode($resultado);
                exit();
                break;
        }
    }
}

// Manejar peticiones GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $controller = new ProyectoController();
    
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'delete':
                $id = $_GET['id'] ?? 0;
                
                // Solo admin puede eliminar proyectos
                if ($rol !== 'admin') {
                    header('Location: ../views/proyectos/listar.php?error=permission');
                    exit();
                }
                
                $resultado = $controller->eliminar($id);
                
                if ($resultado['success']) {
                    header('Location: ../views/proyectos/listar.php?success=deleted');
                } else {
                    header('Location: ../views/proyectos/listar.php?error=' . urlencode($resultado['message']));
                }
                exit();
                break;
                
            case 'get':
                $id = $_GET['id'] ?? 0;
                $proyecto = $controller->obtener($id);
                
                // Verificar permisos de lectura
                if ($proyecto && ($rol === 'gestor' && $proyecto['gestor_id'] != $usuario_id) ||
                    ($rol === 'colaborador' && !$this->usuarioTieneTareasEnProyecto($usuario_id, $id)) ||
                    ($rol === 'cliente' && $proyecto['cliente_id'] != $usuario_id)) {
                    $proyecto = null;
                }
                
                header('Content-Type: application/json');
                echo json_encode($proyecto);
                exit();
                break;
                
            case 'list':
                $proyectos = $controller->listar($usuario_id, $rol);
                header('Content-Type: application/json');
                echo json_encode($proyectos);
                exit();
                break;
                
            case 'stats':
                $estadisticas = $controller->obtenerEstadisticas($usuario_id, $rol);
                header('Content-Type: application/json');
                echo json_encode($estadisticas);
                exit();
                break;
                
            case 'search':
                $termino = $_GET['q'] ?? '';
                $resultados = $controller->buscar($termino, $usuario_id, $rol);
                header('Content-Type: application/json');
                echo json_encode($resultados);
                exit();
                break;
        }
    }
}

// Función auxiliar para verificar si un usuario tiene tareas en un proyecto
function usuarioTieneTareasEnProyecto($usuario_id, $proyecto_id) {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT COUNT(*) as count FROM tareas WHERE asignado_a = ? AND proyecto_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$usuario_id, $proyecto_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}
?>
<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . "/../models/Tarea.php";
require_once __DIR__ . "/../config/auth.php";

class TareaController {
    private $tareaModel;

    public function __construct() {
        $this->tareaModel = new Tarea();
    }

    // Crear tarea
    public function crear($data) {
        // Verificar que el rol tenga permiso
        requireRole(['gestor', 'administrador']);

        // Validar campos obligatorios
        if (empty($data['nombre']) || empty($data['proyecto_id']) || empty($data['lista_id'])) {
            $_SESSION['error'] = "El nombre, proyecto y lista son obligatorios.";
            header("Location: " . url("router.php?page=tareas/tablero&proyecto_id=" . ($data['proyecto_id'] ?? '')));
            exit();
        }

        // Preparar datos para la inserción
        $tareaData = [
            'nombre'        => $data['nombre'],
            'descripcion'   => $data['descripcion'] ?? null,
            'proyecto_id'   => $data['proyecto_id'],
            'lista_id'      => $data['lista_id'],
            'asignado_a'    => $data['asignado_a'] ?? null,
            'fecha_inicio'  => $data['fecha_inicio'] ?? null,
            'fecha_fin'     => $data['fecha_fin'] ?? null,
            'estado'        => $data['estado'] ?? 'pendiente',
            'prioridad'     => $data['prioridad'] ?? 'media'
        ];

        // Insertar tarea usando el modelo
        try {
            $resultado = $this->tareaModel->crear($tareaData);
            
            if ($resultado) {
                $_SESSION['success'] = "Tarea creada correctamente.";
            } else {
                $_SESSION['error'] = "Error al crear la tarea.";
            }
            
        } catch (Exception $e) {
            error_log("Error creando tarea: " . $e->getMessage());
            $_SESSION['error'] = "Error al crear la tarea: " . $e->getMessage();
        }

        // Redirigir al tablero del proyecto - SIN ../ porque ya estamos en la raíz
        header("Location: " . url("router.php?page=tareas/tablero&proyecto_id=" . $data['proyecto_id']));
        exit();
    }

    // Editar tarea
    public function editar($id, $data) {
        requireRole(["gestor"]);

        // Ajustar campos vacíos
        $data['asignado_a']   = empty($data['asignado_a']) ? null : $data['asignado_a'];
        $data['fecha_inicio'] = empty($data['fecha_inicio']) ? null : $data['fecha_inicio'];
        $data['fecha_fin']    = empty($data['fecha_fin']) ? null : $data['fecha_fin'];
        $data['descripcion']  = $data['descripcion'] ?? null;
        $data['estado']       = $data['estado'] ?? 'pendiente';
        $data['prioridad']    = $data['prioridad'] ?? 'media';

        // Intentar actualizar la tarea
        try {
            $this->tareaModel->editar($id, $data);
            $_SESSION['success'] = "Tarea actualizada correctamente.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al actualizar la tarea: " . $e->getMessage();
            echo "<pre>" . $e->getMessage() . "</pre>";
            exit();
        }

        header("Location: ../router.php?page=tareas/tablero&proyecto_id=" . $data['proyecto_id']);
        exit();
    }

    // Eliminar tarea
    public function eliminar($id, $proyecto_id) {
        requireRole(["gestor"]);
        $this->tareaModel->eliminar($id);
        header("Location: ../router.php?page=tareas/tablero&proyecto_id=$proyecto_id");
        exit();
    }

    // Mover tarea (drag & drop)
    public function mover() {

        error_log("DEBUG mover(): inicio de ejecución");

        // Limpiar cualquier output previo
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Configurar headers para JSON
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            // Debug temporal
            error_log("=== MOVER TAREA DEBUG ===");
            error_log("POST: " . print_r($_POST, true));
            error_log("GET: " . print_r($_GET, true));
            error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
            
            // Validar método
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                exit();
            }
            
            // Validar datos de entrada
            if (!isset($_POST['tarea_id']) || !isset($_POST['lista_id'])) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos - tarea_id y lista_id requeridos']);
                exit();
            }
            
            $tarea_id = (int)$_POST['tarea_id'];
            $lista_id = (int)$_POST['lista_id'];
            
            error_log("Tarea ID: $tarea_id, Lista ID: $lista_id");
            
            if ($tarea_id <= 0 || $lista_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'IDs inválidos']);
                exit();
            }
            
            // Verificar que el modelo existe
            if (!$this->tareaModel) {
                echo json_encode(['success' => false, 'message' => 'Modelo no inicializado']);
                exit();
            }
            
            // Ejecutar actualización
            $resultado = $this->tareaModel->actualizarLista($tarea_id, $lista_id);
            
            error_log("Resultado actualización: " . ($resultado ? 'true' : 'false'));
            
            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Tarea movida correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar en base de datos']);
            }
            
        } catch (Exception $e) {
            error_log("ERROR en mover(): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
        
        exit();
        
    }

}
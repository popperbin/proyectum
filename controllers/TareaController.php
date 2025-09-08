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
            header("Location: ../router.php?page=tareas/tablero&proyecto_id=" . ($data['proyecto_id'] ?? ''));
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
            $this->tareaModel->crear($tareaData);
            $_SESSION['success'] = "Tarea creada correctamente.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al crear la tarea: " . $e->getMessage();
            echo $e->getMessage();
            exit();
        }

        // Redirigir al tablero del proyecto
        header("Location: ../router.php?page=tareas/tablero&proyecto_id=" . ($data['proyecto_id'] ?? ''));
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
        if ($_POST['tarea_id'] && $_POST['lista_id']) {
            $resultado = $this->tareaModel->actualizarLista($_POST['tarea_id'], $_POST['lista_id']);
            echo json_encode(['success' => $resultado]);
            exit(); // IMPORTANTE: evitar redirección adicional
        }
    }

    
}


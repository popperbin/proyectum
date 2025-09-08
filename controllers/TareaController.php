<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . "/../models/Tarea.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/Lista.php";


class TareaController {
    private $tareaModel;

    public function __construct() {
        $this->tareaModel = new Tarea();
    }

    // Crear tarea
    public function crear($data) {
        requireRole(['gestor', 'administrador']);

        if (empty($data['nombre']) || empty($data['proyecto_id']) || empty($data['lista_id'])) {
            $_SESSION['error'] = "El nombre, proyecto y lista son obligatorios.";
            return false;
        }

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

        try {
            $this->tareaModel->crear($tareaData);
            $_SESSION['success'] = "Tarea creada correctamente.";
            return true;
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al crear la tarea: " . $e->getMessage();
            return false;
        }
    }

    // Editar tarea
    public function editar($id, $data) {
        requireRole(["gestor"]);

        $data['asignado_a']   = empty($data['asignado_a']) ? null : $data['asignado_a'];
        $data['fecha_inicio'] = empty($data['fecha_inicio']) ? null : $data['fecha_inicio'];
        $data['fecha_fin']    = empty($data['fecha_fin']) ? null : $data['fecha_fin'];
        $data['descripcion']  = $data['descripcion'] ?? null;
        $data['estado']       = $data['estado'] ?? 'pendiente';
        $data['prioridad']    = $data['prioridad'] ?? 'media';

        try {
            $this->tareaModel->editar($id, $data);
            $_SESSION['success'] = "Tarea actualizada correctamente.";
            return true;
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al actualizar la tarea: " . $e->getMessage();
            return false;
        }
    }

    // Eliminar tarea
    public function eliminar($id, $proyecto_id) {
        requireRole(["gestor"]);

        try {
            $this->tareaModel->eliminar($id);
            $_SESSION['success'] = "Tarea eliminada correctamente.";
            return true;
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al eliminar la tarea: " . $e->getMessage();
            return false;
        }
    }
    public function eliminarLista($listaId, $proyectoId) {
        $listaModel = new Lista();
        $listaModel->eliminar($listaId, $proyectoId);

        header("Location: router.php?page=tareas/tablero&proyecto_id=$proyectoId");
        exit();
    }
    public function listar($proyecto_id) {
        if (!$proyecto_id) {
            return [];
        }
     return $this->tareaModel->listarPorProyecto($proyecto_id);
    }


    // Mover tarea (drag & drop)
    public function mover() {
        if (!empty($_POST['tarea_id']) && !empty($_POST['lista_id'])) {
            $resultado = $this->tareaModel->actualizarLista($_POST['tarea_id'], $_POST['lista_id']);
            echo json_encode(['success' => $resultado]);
            exit();
        }
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit();
    }
}
?>
<?php
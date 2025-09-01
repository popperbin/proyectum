<?php
require_once __DIR__ . "/../models/Tarea.php";
require_once __DIR__ . "/../config/auth.php";

class TareaController {
    private $tareaModel;

    public function __construct() {
        $this->tareaModel = new Tarea();
    }

    // Crear tarea
    public function crear($data) {
    requireRole(["gestor"]);
    if (empty($data['nombre'])) {
        $_SESSION['error'] = "El nombre de la tarea es obligatorio.";
        header("Location: ../views/tareas/tablero.php?proyecto_id=" . $data['proyecto_id']);
        exit();
        }

    $this->tareaModel->crear([
        'proyecto_id' => $data['proyecto_id'],
        'lista_id' => $data['lista_id'],
        'nombre' => $data['nombre'],
        'descripcion' => $data['descripcion'] ?? null,
        'asignado_a' => $data['asignado_a'] ?? null,
        'fecha_inicio' => $data['fecha_inicio'] ?? null,
        'fecha_fin' => $data['fecha_fin'] ?? null,
        'estado' => $data['estado'] ?? 'pendiente',
        'prioridad' => $data['prioridad'] ?? 'media'
    ]);


        header("Location: ../views/tareas/tablero.php?proyecto_id=" . $data['proyecto_id']);
        exit();
    }

    // Editar tarea
    public function editar($id, $data) {
        requireRole(["gestor"]);
        $this->tareaModel->editar($id, $data);
        header("Location: ../views/tareas/tablero.php?proyecto_id=" . $data['proyecto_id']);
        exit();
    }

    // Eliminar tarea
    public function eliminar($id, $proyecto_id) {
        requireRole(["gestor"]);
        $this->tareaModel->eliminar($id);
        header("Location: ../views/tareas/tablero.php?proyecto_id=$proyecto_id");
        exit();
    }

    // Mover tarea (drag & drop)
    public function mover($id, $lista_id) {
        requireRole(["gestor"]);
        $this->tareaModel->mover($id, $lista_id);
        echo "ok";
        exit();
    }
}

// --- Router básico ---
if (isset($_GET['accion'])) {
    $controller = new TareaController();

    switch ($_GET['accion']) {
        case 'crear':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
            }
            break;

        case 'editar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $controller->editar($_GET['id'], $_POST);
            }
            break;

        case 'eliminar':
            if (isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->eliminar($_GET['id'], $_GET['proyecto_id']);
            }
            break;

        case 'mover':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->mover($_POST['id'], $_POST['lista_id']);
            }
            break;
    }
}


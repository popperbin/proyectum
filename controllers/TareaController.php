<?php
require_once __DIR__ . "/../models/Tarea.php";

class TareaController {
    private $tarea;

    public function __construct() {
        $this->tarea = new Tarea();
    }

    public function listar($proyecto_id) {
        return $this->tarea->listarPorProyecto($proyecto_id);
    }

    public function crear($data) {
        return $this->tarea->crear($data);
    }

    public function actualizarEstado($id, $estado) {
        return $this->tarea->actualizarEstado($id, $estado);
    }
}

if (isset($_GET['accion'])) {
    $controller = new TareaController();

    if ($_GET['accion'] === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->crear($_POST);
        header("Location: ../views/tareas/tablero.php?proyecto_id=" . $_POST['proyecto_id']);
        exit();
    }

    if ($_GET['accion'] === 'estado' && isset($_GET['id']) && isset($_GET['estado'])) {
        $controller->actualizarEstado($_GET['id'], $_GET['estado']);
        header("Location: ../views/tareas/tablero.php?proyecto_id=" . $_GET['proyecto_id']);
        exit();
    }
}


<?php
require_once __DIR__ . "/../models/Lista.php";
require_once __DIR__ . "/../config/auth.php";

class ListaController {
    private $listaModel;

    public function __construct() {
        $this->listaModel = new Lista();
    }

    // Crear lista
    public function crear($nombre, $proyecto_id) {
        requireRole(["gestor"]);
        $this->listaModel->crear($nombre, $proyecto_id);
        header("Location: ../views/tareas/tablero.php?proyecto_id=$proyecto_id");
        exit();
    }

    // Eliminar lista
    public function eliminar($id, $proyecto_id) {
        requireRole(["gestor"]);
        $this->listaModel->eliminar($id);
        header("Location: ../views/tareas/tablero.php?proyecto_id=$proyecto_id");
        exit();
    }
}

// --- Router básico ---
if (isset($_GET['accion'])) {
    $controller = new ListaController();

    switch ($_GET['accion']) {
        case 'crear':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST['nombre'], $_POST['proyecto_id']);
            }
            break;

        case 'eliminar':
            if (isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->eliminar($_GET['id'], $_GET['proyecto_id']);
            }
            break;
    }
}

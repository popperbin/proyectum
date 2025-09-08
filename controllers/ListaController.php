<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . "/../models/Lista.php";
require_once __DIR__ . "/../config/auth.php";

class ListaController {
    private $listaModel;

    public function __construct() {
        $this->listaModel = new Lista();
    }

    // Crear lista
    public function crear() {

        // Verificar que el rol tenga permiso
        requireRole(['gestor', 'administrador']);

        if ($_POST['nombre'] && $_POST['proyecto_id']) {
            $lista = new Lista();
            $resultado = $lista->crear($_POST['nombre'], $_POST['proyecto_id']);
            header("Location: ../router.php?page=tareas/tablero&proyecto_id=" . $_POST['proyecto_id']);
        }
    }

    // archivar lista
    public function archivar($id, $proyecto_id) {        
        requireRole(["gestor", "administrador"]);
            $this->listaModel->archivar($id, $proyecto_id);
            $_SESSION['success'] = "Lista archivada correctamente.";

        header("Location: ../router.php?page=tareas/tablero&proyecto_id=$proyecto_id");
        exit();
    }

}

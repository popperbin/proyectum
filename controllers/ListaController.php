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

        // Validamos los datos del formulario
        if (isset($_POST['nombre'], $_POST['proyecto_id']) && !empty($_POST['nombre']) && !empty($_POST['proyecto_id'])) {
            // Si los datos son válidos, procedemos a crear la lista
            $lista = new Lista();
            $resultado = $lista->crear($_POST['nombre'], $_POST['proyecto_id']);

            // Comprobamos si la inserción fue exitosa
            if ($resultado) {
                $_SESSION['success'] = "Lista creada correctamente.";
                header("Location: router.php?page=tareas/tablero&proyecto_id=" . $_POST['proyecto_id']);
            } else {
                // Si hubo un error al crear la lista
                $_SESSION['error'] = "Hubo un problema al crear la lista.";
                header("Location: router.php?page=proyectos/detalle&proyecto_id=" . $_POST['proyecto_id']);
            }
        } else {
            // Si faltan datos
            $_SESSION['error'] = "Faltan datos necesarios.";
            header("Location: router.php?page=proyectos/detalle&proyecto_id=" . $_POST['proyecto_id']);
        }
    }

    // Archivar lista
    public function archivar($id, $proyecto_id) {        
        requireRole(["gestor", "administrador"]);
        $this->listaModel->archivar($id, $proyecto_id);
        $_SESSION['success'] = "Lista archivada correctamente.";
        header("Location: router.php?page=tareas/tablero&proyecto_id=$proyecto_id");
        exit();
    }
}
